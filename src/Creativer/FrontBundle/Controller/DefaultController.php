<?php

namespace Creativer\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Creativer\FrontBundle\Entity\User;
use Creativer\FrontBundle\Entity\Wall;
use Creativer\FrontBundle\Entity\Role;
use Creativer\FrontBundle\Entity\Images;
use Creativer\FrontBundle\Entity\Albums;
use Creativer\FrontBundle\Entity\PostBaraholka;
use Creativer\FrontBundle\Entity\ImagesBaraholka;
use Creativer\FrontBundle\Entity\PostImages;
use Creativer\FrontBundle\Entity\PostCategory;
use Creativer\FrontBundle\Entity\PostCity;
use Creativer\FrontBundle\Entity\Posts;
use Creativer\FrontBundle\Services\ImageServices;
use Imagine\Image\Box;
use Imagine\Imagick;
use Imagine\Image\ImageInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response as Respon;




class DefaultController extends Controller
{

    public function registrationAction()
    {
        $request = $this->get('request');

        $form = $this->createFormBuilder(null, array(
            'data_class' => 'Creativer\FrontBundle\Entity\User',
                ))->add('username', 'text', array('required'=>false))
                  ->add('lastname', 'text', array('required'=>false))
                  ->add('password', 'repeated', array('type' => 'password', 'invalid_message' => 'Ваш пароль не совпадает',))
                  ->add('email', 'email', array('required'=>false))
                  ->getForm();



        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);

            foreach ($form as $child) {
                if (!$child->isValid()) {
                    if(!empty($child->getErrors()[0])){
                        $errors[] = $child->getErrors()[0]->getMessage();
                    }
                }
            }


            if(isset($form->get('password')['first']->getErrors()[0]))
                $errors[] = $form->get('password')['first']->getErrors()[0]->getMessage();


            if(!empty($errors)){
                $session = $request->getSession();

                return $this->render('CreativerFrontBundle:Default:registration.html.twig', array('form' => $form->createView(),
                    'errors' => $errors, 'img' => $request->get('form')['img']));
            }else{
                $im = new ImageServices($this->container);
                $img = $im->base64_to_jpeg($request->get('form')['img']);
            }

            $factory = $this->get('security.encoder_factory');
            $user = new User();
            $wall = new Wall();
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($request->get('form')['password']['first'], $user->getSalt());
            $user->setAvatar($img);
            $user->setUsername($request->get('form')['username']);
            $user->setLastname($request->get('form')['lastname']);
            $user->setEmail($request->get('form')['email']);
            $user->setPassword($password);
            $user->setWall($wall);
            $wall->setUser($user);


            $em = $this->getDoctrine()->getManager();
            $role = $em->getRepository('CreativerFrontBundle:Role')->findOneByName('USER');
            $user->addRole($role);

            $em->persist($wall);
            $em->persist($user);
            $em->persist($role);
            $em->flush();

            if ($user) {
                $token = new UsernamePasswordToken(
                    $user,
                    null,
                    'secured_area',
                    $user->getRoles());

                $this->get('security.context')->setToken($token);
                $this->get('session')->set('_security_secured_area',serialize($token));
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
            }


            $url = $this->generateUrl('creativer_front_main');
            return new RedirectResponse($url);
           // return $this->forward('CreativerFrontBundle:Default:security_check', array('email' => $request->get('email')));

        }else {
            if ($this->get('security.context')->isGranted('ROLE_USER')) {
                    return $this->forward('CreativerFrontBundle:Default:main');
                }
                return $this->render('CreativerFrontBundle:Default:registration.html.twig', array('form' => $form->createView(), 'errors' => null));
        }

    }

    public function security_checkAction(){

        $url = $this->generateUrl('creativer_front_main');
        return new RedirectResponse($url);
    }

    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // получить ошибки логина, если таковые имеются
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('CreativerFrontBundle::login.html.twig', array(
            // имя, введённое пользователем в последний раз
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }

    public function createAlbumTmpAction(){

        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $userId = $this->get('security.context')->getToken()->getUser()->getId();

        return $this->render('CreativerFrontBundle:Default:createAlbumTmp.html.twig',  array('id' => $userId));
    }

    public function editAlbumTmpAction(){

        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $userId = $this->get('security.context')->getToken()->getUser()->getId();

        return $this->render('CreativerFrontBundle:Default:editAlbumTmp.html.twig',  array('id' => $userId));
    }

    public function uploadAlbumAction(){

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $image = $request->files->get('file');
            $main = $request->get('main');
            $price = $request->get('price');
            $title = $request->get('title');

            if (($image instanceof UploadedFile) && ($image->getError() == '0')) {
                if (($image->getSize() < 2000000000)) {
                    $originalName = $image->getClientOriginalName();
                    $file_type = $image->getMimeType();
                    $valid_filetypes = array('image/jpg', 'image/jpeg', 'image/bmp', 'image/png');
                    if (in_array(strtolower($file_type), $valid_filetypes)) {
                        //Start Uploading File
                        $userId = $this->get('security.context')->getToken()->getUser()->getId();
                        $em = $this->getDoctrine()->getEntityManager();
                        $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findBy(array('id'=>$userId));
                        //die(\Doctrine\Common\Util\Debug::dump($image));
                        $album = $em->getRepository("CreativerFrontBundle:Albums")->findBy(array('isActive' => 0, 'user' => $user[0]));

                        if(empty($album)){
                            $album = new Albums();
                            $album->setUser($user[0]);
                        }else{
                            $album = $album[0];
                        }

                        try {
                            $imagine = new \Imagine\Imagick\Imagine();
                            $image_name = time() . "_" . md5($originalName) . '.jpg';

                            $image = $imagine->open($image->getPathname());

                            $image->save($this->container->getParameter('path_img_album_original') . $image_name);

                            $w = $image->getSize()->getWidth();
                            $h = $image->getSize()->getHeight();

                            if($w > $h){
                                $count = $image->getSize()->getHeight() / 178;
                                $width = $image->getSize()->getWidth() / $count;
                                $image->resize(new Box($width, 178), ImageInterface::FILTER_LANCZOS);
                            }else{
                                $count = $image->getSize()->getWidth() / 158;
                                $height = $image->getSize()->getHeight() / $count;
                                $image->resize(new Box(158, $height), ImageInterface::FILTER_LANCZOS);
                            }

                            $image->save($this->container->getParameter('path_img_album_thums') . $image_name);


                            if($main == 1){
                                $album->setImg($image_name);
                            }

                            $im = new Images();
                            $im->setAlbum($album);
                            $im->setName($image_name);
                            if($price != null)
                            $im->setPrice($price);
                            if($title != null)
                            $im->setText($title);


                            $em->persist($album);
                            $em->persist($im);
                            $em->flush();
                        } catch (\Imagine\Exception\Exception $e) {
                            die("error upload image ".$e);
                        }

                    } else {
                        $status = 'failed';
                        $message = 'Invalid File Type';
                    }
                } else {
                    $status = 'failed';
                    $message = 'Size exceeds limit';
                }
            } else {
                $status = 'failed';
                $message = 'File Error';
            }
            $em->persist($album);
            $em->flush();
            $id = $album->getId();
            $array = array('id' => $id);
            $response = new Respon(json_encode($array), 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            return $this->render('CreativerFrontBundle:Default:createAlbumTmp.html.twig');
        }

        return $this->render('CreativerFrontBundle:Default:createAlbumTmp.html.twig');
    }

    public function uploadEditAlbumAction(){

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $image = $request->files->get('file');
            $id_album = $request->get('id_album');

            if (($image instanceof UploadedFile) && ($image->getError() == '0')) {
                if (($image->getSize() < 2000000000)) {
                    $originalName = $image->getClientOriginalName();
                    $file_type = $image->getMimeType();
                    $valid_filetypes = array('image/jpg', 'image/jpeg', 'image/bmp', 'image/png');
                    if (in_array(strtolower($file_type), $valid_filetypes)) {
                        //Start Uploading File
                        $userId = $this->get('security.context')->getToken()->getUser()->getId();
                        $em = $this->getDoctrine()->getEntityManager();
                        //$user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findBy(array('id'=>$userId));
                        $album = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')->find($id_album);

                        try {
                            $imagine = new \Imagine\Imagick\Imagine();
                            $image_name = time() . "_" . md5($originalName) . '.jpg';

                            $image = $imagine->open($image->getPathname());
                            $image->save($this->container->getParameter('path_img_album_original') . $image_name);

                            $w = $image->getSize()->getWidth();
                            $h = $image->getSize()->getHeight();

                            if($w > $h){
                                $count = $image->getSize()->getHeight() / 178;
                                $width = $image->getSize()->getWidth() / $count;
                                $image->resize(new Box($width, 178), ImageInterface::FILTER_LANCZOS);
                            }else{
                                $count = $image->getSize()->getWidth() / 158;
                                $height = $image->getSize()->getHeight() / $count;
                                $image->resize(new Box(158, $height), ImageInterface::FILTER_LANCZOS);
                            }

                            $image->save($this->container->getParameter('path_img_album_thums') . $image_name);

                            $im = new Images();
                            $im->setAlbum($album);
                            $im->setName($image_name);
                            $em->persist($album);
                            $em->persist($im);
                            $em->flush();

                            $serializer = $this->container->get('jms_serializer');
                            $categories = $serializer
                                ->serialize(
                                    $im,
                                    'json',
                                    SerializationContext::create()
                                        ->setGroups(array('uploadEditAlbum'))
                                );

                            $response = new Respon($categories);
                            $response->headers->set('Content-Type', 'application/json');
                            return $response;
                        } catch (\Imagine\Exception\Exception $e) {
                            die("error upload image ".$e);
                        }

                    } else {
                        $status = 'failed';
                        $message = 'Invalid File Type';
                    }
                } else {
                    $status = 'failed';
                    $message = 'Size exceeds limit';
                }
            } else {
                $status = 'failed';
                $message = 'File Error';
            }

            $response = new Respon(null, 500);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            return $this->render('CreativerFrontBundle:Default:createAlbumTmp.html.twig');
        }

        return $this->render('CreativerFrontBundle:Default:createAlbumTmp.html.twig');
    }

    public function layout_frontAction(){

        return $this->render('CreativerFrontBundle::layout_front.html.twig', array());
    }

    public function mainTmpAction()
    {
        return $this->render('CreativerFrontBundle:Default:mainTmp.html.twig', array());
    }

    public function personTmpAction($id)
    {
        return $this->render('CreativerFrontBundle:Default:personTmp.html.twig', array('id' => $id));
    }

    public function albumTmpAction($id_album){

        $em = $this->getDoctrine()->getEntityManager();

        $album = $em->getRepository("CreativerFrontBundle:Albums")->find($id_album);

        $user_id = $album->getUser()->getId();

        return $this->render('CreativerFrontBundle:Default:albumTmp.html.twig', array('id' => $user_id));
    }

    public function baraholkaTmpAction(){

        return $this->render('CreativerFrontBundle:Default:baraholkaTmp.html.twig', array());
    }

    public function eventsTmpAction(){

        return $this->render('CreativerFrontBundle:Default:eventsTmp.html.twig', array());
    }

    public function createEventTmpAction(){

        return $this->render('CreativerFrontBundle:Default:createEventTmp.html.twig', array());
    }

    public function peopleTmpAction(){

        return $this->render('CreativerFrontBundle:Default:peopleTmp.html.twig', array());
    }

    public function fleamarketpostingTmpAction(){

        $user = $this->get('security.context')->getToken()->getUser();

        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $response = new Response(null, 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $post_baraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->findBy(array('user'=>$user,'isActive'=>0));

        if(!empty($post_baraholka)){
            $post_baraholka = $post_baraholka[0];
        }

        if(empty($post_baraholka)){
            $post_baraholka = new PostBaraholka();
            $post_baraholka->setUser($user);
            $em->persist($post_baraholka);
            $em->flush();
        }

        //die(\Doctrine\Common\Util\Debug::dump($post_baraholka));
        $post_id = $post_baraholka->getId();

        return $this->render('CreativerFrontBundle:Default:fleamarketpostingTmp.html.twig', array('post_id' => $post_id));
    }

    public function editFleamarketpostingTmpAction(){

        $user = $this->get('security.context')->getToken()->getUser();

        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $response = new Response(null, 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $post_baraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->findBy(array('user'=>$user,'isActive'=>0));

        if(!empty($post_baraholka)){
            $post_baraholka = $post_baraholka[0];
        }

        if(empty($post_baraholka)){
            $post_baraholka = new PostBaraholka();
            $post_baraholka->setUser($user);
            $em->persist($post_baraholka);
            $em->flush();
        }

        $post_id = $post_baraholka->getId();

        return $this->render('CreativerFrontBundle:Default:editFleamarketpostingTmp.html.twig', array('post_id' => $post_id));
    }

    public function viewforumTmpAction(){

        $user = $this->get('security.context')->getToken()->getUser();

        return $this->render('CreativerFrontBundle:Default:viewforumTmp.html.twig', array());
    }

    public function viewtopicTmpAction(){

        $user = $this->get('security.context')->getToken()->getUser();

        return $this->render('CreativerFrontBundle:Default:viewtopicTmp.html.twig', array());
    }

    public function createPostBaraholkaAction(){

        $user = $this->get('security.context')->getToken()->getUser();

        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $response = new Response(null, 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }


        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $image = $request->files->get('file');
            $post_id = $request->get('post_id');
            $post_category = $request->get('post_category');
            $section = $request->get('section');
            $name = $request->get('title');
            $city = $request->get('city');
            $description = $request->get('description');
            $full_description = $request->get('full_description');
            $full_price = $request->get('full_price');
            $main = $request->get('main');
            if($request->get('auction') == 'true'){
                $auction = 1;
            }else{
                $auction = 0;
            }



            $em = $this->getDoctrine()->getEntityManager();
            $PostBaraholka = $em->getRepository("CreativerFrontBundle:PostBaraholka")->find(array('id' => $post_id, 'user' => $user));

            $PostCategory = $em->getRepository("CreativerFrontBundle:PostCategory")->find($post_category);
            $PostCity = $em->getRepository("CreativerFrontBundle:PostCity")->find($city);
            $CategoriesBaraholka = $em->getRepository("CreativerFrontBundle:CategoriesBaraholka")->find($section);

           // die(\Doctrine\Common\Util\Debug::dump($CategoriesBaraholka));

            $PostBaraholka->setCategoriesBaraholka($CategoriesBaraholka);
            $PostBaraholka->setPostCategory($PostCategory);
            $PostBaraholka->setPostCity($PostCity);
            $PostBaraholka->setName($name);
            $PostBaraholka->setDescription($description);
            $PostBaraholka->setFullDescription($full_description);
            $PostBaraholka->setPrice($full_price);
            $PostBaraholka->setIsActive(1);
            $PostBaraholka->setAuction($auction);




            if (($image instanceof UploadedFile) && ($image->getError() == '0')) {
                if (($image->getSize() < 2000000000)) {
                    $originalName = $image->getClientOriginalName();
                    $file_type = $image->getMimeType();
                    $valid_filetypes = array('image/jpg', 'image/jpeg', 'image/bmp', 'image/png');
                    if (in_array(strtolower($file_type), $valid_filetypes)) {
                        //die(\Doctrine\Common\Util\Debug::dump($image));

                        try {
                            $imagine = new \Imagine\Imagick\Imagine();
                            $image_name = time() . "_" . md5($originalName) . '.jpg';

                            $image = $imagine->open($image->getPathname());

                            $image->save($this->container->getParameter('path_img_baraholka_original') . $image_name);

                            $w = $image->getSize()->getWidth();
                            $h = $image->getSize()->getHeight();

                            if($w > $h){
                                $count = $image->getSize()->getHeight() / 178;
                                $width = $image->getSize()->getWidth() / $count;
                                $image->resize(new Box($width, 178), ImageInterface::FILTER_LANCZOS);
                            }else{
                                $count = $image->getSize()->getWidth() / 158;
                                $height = $image->getSize()->getHeight() / $count;
                                $image->resize(new Box(158, $height), ImageInterface::FILTER_LANCZOS);
                            }

                            $image->save($this->container->getParameter('path_img_baraholka_thums') . $image_name);


                            if($main == 1){
                                $PostBaraholka->setImg($image_name);
                            }

                            $im = new ImagesBaraholka();
                            $im->setPostBaraholka($PostBaraholka);
                            $im->setName($image_name);

                            $em->persist($im);


                        } catch (\Imagine\Exception\Exception $e) {
                            die("error upload image ".$e);
                        }


                    } else {
                        $status = 'failed';
                        $message = 'Invalid File Type';
                    }
                } else {
                    $status = 'failed';
                    $message = 'Size exceeds limit';
                }
            } else {
                $status = 'failed';
                $message = 'File Error';
            }


            $em->persist($PostBaraholka);
            $em->flush();
            $id = $PostBaraholka->getId();
            $array = array('id' => $id);
            $response = new Respon(json_encode($array), 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }


    }

    public function editImagesPostBaraholkaAction(){

        $user = $this->get('security.context')->getToken()->getUser();

        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $response = new Response(null, 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $image = $request->files->get('file');
            $id = $request->get('id');

            $em = $this->getDoctrine()->getEntityManager();
            $PostBaraholka = $em->getRepository("CreativerFrontBundle:PostBaraholka")->find(array('id' => $id, 'user' => $user));


            if (($image instanceof UploadedFile) && ($image->getError() == '0')) {
                if (($image->getSize() < 2000000000)) {
                    $originalName = $image->getClientOriginalName();
                    $file_type = $image->getMimeType();
                    $valid_filetypes = array('image/jpg', 'image/jpeg', 'image/bmp', 'image/png');
                    if (in_array(strtolower($file_type), $valid_filetypes)) {
                        //die(\Doctrine\Common\Util\Debug::dump($image));
                        try {
                            $imagine = new \Imagine\Imagick\Imagine();
                            $image_name = time() . "_" . md5($originalName) . '.jpg';

                            $image = $imagine->open($image->getPathname());

                            $image->save($this->container->getParameter('path_img_baraholka_original') . $image_name);

                            $w = $image->getSize()->getWidth();
                            $h = $image->getSize()->getHeight();

                            if($w > $h){
                                $count = $image->getSize()->getHeight() / 178;
                                $width = $image->getSize()->getWidth() / $count;
                                $image->resize(new Box($width, 178), ImageInterface::FILTER_LANCZOS);
                            }else{
                                $count = $image->getSize()->getWidth() / 158;
                                $height = $image->getSize()->getHeight() / $count;
                                $image->resize(new Box(158, $height), ImageInterface::FILTER_LANCZOS);
                            }

                            $image->save($this->container->getParameter('path_img_baraholka_thums') . $image_name);


                            $im = new ImagesBaraholka();
                            $im->setName($image_name);
                            $PostBaraholka->addImagesBaraholka($im);
                            $im->setPostBaraholka($PostBaraholka);
                            $em->persist($im);
                            $em->flush();


                            $serializer = $this->container->get('jms_serializer');
                            $categories = $serializer
                                ->serialize(
                                    $im,
                                    'json'
                                );

                            $response = new Respon($categories);
                            $response->headers->set('Content-Type', 'application/json');
                            return $response;

                        } catch (\Imagine\Exception\Exception $e) {
                            die("error upload image ".$e);
                        }


                    } else {
                        $status = 'failed';
                        $message = 'Invalid File Type';
                    }
                } else {
                    $status = 'failed';
                    $message = 'Size exceeds limit';
                }
            } else {
                $status = 'failed';
                $message = 'File Error';
            }

            $response = new Respon(null, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

    }

    public function savePostImagesAction(){

        $user = $this->get('security.context')->getToken()->getUser();

        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $response = new Response(null, 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $image = $request->files->get('file');
            $post_id = $request->get('post_id');

            $em = $this->getDoctrine()->getEntityManager();
            $Post = $em->getRepository("CreativerFrontBundle:Posts")->find(array('id' => $post_id, 'user' => $user));


            if (($image instanceof UploadedFile) && ($image->getError() == '0')) {
                if (($image->getSize() < 2000000000)) {
                    $originalName = $image->getClientOriginalName();
                    $file_type = $image->getMimeType();
                    $valid_filetypes = array('image/jpg', 'image/jpeg', 'image/bmp', 'image/png', 'image/gif');
                    if (in_array(strtolower($file_type), $valid_filetypes)) {
                        //die(\Doctrine\Common\Util\Debug::dump($image));
                        try {
                            $imagine = new \Imagine\Imagick\Imagine();
                            $image_name = time() . "_" . md5($originalName) . '.jpg';

                            $image = $imagine->open($image->getPathname());

                            $image->save($this->container->getParameter('path_img_post_original') . $image_name);

                            $w = $image->getSize()->getWidth();
                            $h = $image->getSize()->getHeight();


                            if($w > 260){
                                $width = $image->getSize()->getWidth();
                                $count = $width / 260;
                                $width = 260;
                                $height = $image->getSize()->getHeight() / $count;
                                $image->resize(new Box($width, $height), ImageInterface::FILTER_LANCZOS);
                            }else{
                                $height = $h;
                                $width = $w;
                                $image->resize(new Box($w, $h), ImageInterface::FILTER_LANCZOS);
                            }

                            $image->save($this->container->getParameter('path_img_post_thums') . $image_name);


                            $im = new PostImages();
                            $im->setName($image_name);
                            $im->setWidth($width);
                            $im->setHeight($height);
                            $Post->addPostImage($im);
                            $im->setPost($Post);
                            $em->persist($im);
                            $em->flush();


                            $serializer = $this->container->get('jms_serializer');
                            $categories = $serializer
                                ->serialize(
                                    $im,
                                    'json'
                                );

                            $response = new Respon($categories);
                            $response->headers->set('Content-Type', 'application/json');
                            return $response;

                        } catch (\Imagine\Exception\Exception $e) {
                            die("error upload image ".$e);
                        }


                    } else {
                        $status = 'failed';
                        $message = 'Invalid File Type';
                    }
                } else {
                    $status = 'failed';
                    $message = 'Size exceeds limit';
                }
            } else {
                $status = 'failed';
                $message = 'File Error';
            }

            $response = new Respon(null, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    public function followingTmpAction(){

        $userId = $this->get('security.context')->getToken()->getUser()->getId();

        return $this->render('CreativerFrontBundle:Default:followingTmp.html.twig', array('id' => $userId));
    }

    public function followersTmpAction(){

        $userId = $this->get('security.context')->getToken()->getUser()->getId();

        return $this->render('CreativerFrontBundle:Default:followersTmp.html.twig', array('id' => $userId));
    }

    public function productsTmpAction(){

        return $this->render('CreativerFrontBundle:Default:productsTmp.html.twig', array());
    }

    public function servicesTmpAction(){


        return $this->render('CreativerFrontBundle:Default:servicesTmp.html.twig', array());
    }

    public function messagesTmpAction(){

        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $userId = $this->get('security.context')->getToken()->getUser()->getId();

        return $this->render('CreativerFrontBundle:Default:messagesTmp.html.twig', array('id' => $userId));
    }

    public function chatTmpAction(){

        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $userId = $this->get('security.context')->getToken()->getUser()->getId();

        return $this->render('CreativerFrontBundle:Default:chatTmp.html.twig', array('id' => $userId));
    }

    public function fleamarketAction(){


        return $this->render('CreativerFrontBundle:Default:fleamarket.html.twig', array());
    }

    public function feedbackTmpAction(){


        return $this->render('CreativerFrontBundle:Default:feedbackTmp.html.twig', array());
    }

    public function userInfoTmpAction($id){


        return $this->render('CreativerFrontBundle:Default:userInfoTmp.html.twig', array('id' => $id));
    }


}
