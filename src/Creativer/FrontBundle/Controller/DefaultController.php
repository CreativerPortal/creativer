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
use Creativer\FrontBundle\Entity\Shops;
use Creativer\FrontBundle\Entity\ImagesShops;
use Creativer\FrontBundle\Entity\Address;
use Creativer\FrontBundle\Entity\PostBaraholka;
use Creativer\FrontBundle\Entity\ImagesBaraholka;
use Creativer\FrontBundle\Entity\PostImages;
use Creativer\FrontBundle\Entity\PostDocuments;
use Creativer\FrontBundle\Entity\PostCategory;
use Creativer\FrontBundle\Entity\PostCity;
use Creativer\FrontBundle\Entity\Posts;
use Creativer\FrontBundle\Entity\Events;
use Symfony\Component\Filesystem\Filesystem;
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
                if($request->get('form')['img'] != "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAB4CAYAAAA5ZDbSAAABp0lEQVR4Xu3TAREAAAiDQNe/tD3+sAHgdh1tYDRdcFdg/AkKXGDcAI7XgguMG8DxWnCBcQM4XgsuMG4Ax2vBBcYN4HgtuMC4ARyvBRcYN4DjteAC4wZwvBZcYNwAjteCC4wbwPFacIFxAzheCy4wbgDHa8EFxg3geC24wLgBHK8FFxg3gOO14ALjBnC8Flxg3ACO14ILjBvA8VpwgXEDOF4LLjBuAMdrwQXGDeB4LbjAuAEcrwUXGDeA47XgAuMGcLwWXGDcAI7XgguMG8DxWnCBcQM4XgsuMG4Ax2vBBcYN4HgtuMC4ARyvBRcYN4DjteAC4wZwvBZcYNwAjteCC4wbwPFacIFxAzheCy4wbgDHa8EFxg3geC24wLgBHK8FFxg3gOO14ALjBnC8Flxg3ACO14ILjBvA8VpwgXEDOF4LLjBuAMdrwQXGDeB4LbjAuAEcrwUXGDeA47XgAuMGcLwWXGDcAI7XgguMG8DxWnCBcQM4XgsuMG4Ax2vBBcYN4HgtuMC4ARyvBRcYN4DjteAC4wZwvBZcYNwAjteCC4wbwPFaMB74AS1+AHmsOhpiAAAAAElFTkSuQmCC"){
                    $img = $im->base64_to_jpeg($request->get('form')['img']);
                }else{
                    $img = '';
                }
            }

            $color = sprintf( '#%02X%02X%02X', rand(0, 255), rand(0, 255), rand(0, 255) );

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
            $user->setRealPassword($request->get('form')['password']['first']);
            $user->setWall($wall);
            $user->setColor($color);
            $user->setAutoscroll(0);
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
            $name_album = $request->get('name_album');
            $description_album = $request->get('description_album');
            $stop = $request->get('stop');
            $selectCategories = $request->get('selectCategories');
            $selectCategories = explode(",", $selectCategories);


            if (($image instanceof UploadedFile) && ($image->getError() == '0')) {
                if (($image->getSize() < 10485760)) {
                    $originalName = $image->getClientOriginalName();
                    $file_type = $image->getMimeType();
                    $valid_filetypes = array('image/jpg', 'image/jpeg', 'image/bmp', 'image/png', 'image/gif');
                    if (in_array(strtolower($file_type), $valid_filetypes)) {
                        //Start Uploading File
                        $userId = $this->get('security.context')->getToken()->getUser()->getId();
                        $em = $this->getDoctrine()->getEntityManager();
                        $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findBy(array('id'=>$userId));
                        $album = $em->getRepository("CreativerFrontBundle:Albums")->findBy(array('isActive' => 0, 'user' => $user[0]));
                        $categories = $em->getRepository("CreativerFrontBundle:Categories")->findById($selectCategories);

                        if(empty($album)){
                            $album = new Albums();
                            $album->setUser($user[0]);
                            $album->setName($name_album);
                            $album->setDescription($description_album);
                            foreach($categories as $cat){
                                $album->addCategory($cat);
                            }
                        }else{
                            $album = $album[0];
                        }
                        if($stop == 'true'){
                            $album->setIsActive(1);
                        }


                        $year = date("Y");
                        $maonth = date("m");
                        $day = date("d");


                        try {
                            $imagine = new \Imagine\Imagick\Imagine();
                            $name_path = $year."/".$maonth."/".$day."/";
                            $image_name = time() . "_" . md5($originalName) . '.jpg';
                            if (!file_exists($this->container->getParameter('path_img_album_original').$name_path))
                                mkdir($this->container->getParameter('path_img_album_original').$name_path, 0777, true);
                            if (!file_exists($this->container->getParameter('path_img_album_thums').$name_path))
                                mkdir($this->container->getParameter('path_img_album_thums').$year."/".$maonth."/".$day, 0777, true);
                            $path = $image->getPathname();
                            $image = $imagine->open($path);
                            $w = $image->getSize()->getWidth();
                            $h = $image->getSize()->getHeight();

                            if($w >= 1280 or $h >= 872){
                                if($w > $h){
                                    $count = $image->getSize()->getHeight() / 872;
                                    $width = $image->getSize()->getWidth() / $count;
                                    $image->resize(new Box($width, 872), ImageInterface::FILTER_LANCZOS);
                                }else{
                                    $count = $image->getSize()->getWidth() / 1280;
                                    $height = $image->getSize()->getHeight() / $count;
                                    $image->resize(new Box(1280, $height), ImageInterface::FILTER_LANCZOS);
                                }
                            }

                            $image->save($this->container->getParameter('path_img_album_original').$originalName);
                            rename($this->container->getParameter('path_img_album_original').$originalName,$this->container->getParameter('path_img_album_original').$name_path.$image_name);
                            if($w > $h){
                                $count = $image->getSize()->getHeight() / 178;
                                $width = $image->getSize()->getWidth() / $count;
                                $image->resize(new Box($width, 178), ImageInterface::FILTER_LANCZOS);
                            }else{
                                $count = $image->getSize()->getWidth() / 158;
                                $height = $image->getSize()->getHeight() / $count;
                                $image->resize(new Box(158, $height), ImageInterface::FILTER_LANCZOS);
                            }


                            $image->save($this->container->getParameter('path_img_album_thums').$originalName);
                            rename($this->container->getParameter('path_img_album_thums').$originalName,$this->container->getParameter('path_img_album_thums').$name_path.$image_name);


                            if($main == 1){
                                $album->setImg($image_name);
                                $album->setPath($name_path);
                            }

                            $im = new Images();
                            $im->setAlbum($album);
                            $im->setPath($name_path);
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
                        $response = new Respon("Error type", 500);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                } else {
                    $response = new Respon("Error size", 500);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
            } else {
                $response = new Respon("Error UploadObject", 500);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
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
                if (($image->getSize() < 10485760)) {
                    $originalName = $image->getClientOriginalName();
                    $file_type = $image->getMimeType();
                    $valid_filetypes = array('image/jpg', 'image/jpeg', 'image/bmp', 'image/png', 'image/gif');
                    if (in_array(strtolower($file_type), $valid_filetypes)) {
                        //Start Uploading File
                        $userId = $this->get('security.context')->getToken()->getUser()->getId();
                        $em = $this->getDoctrine()->getEntityManager();
                        $album = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')->find($id_album);

                        $year = date("Y");
                        $maonth = date("m");
                        $day = date("d");

                        try {
                            $imagine = new \Imagine\Imagick\Imagine();
                            $name_path = $year."/".$maonth."/".$day."/";
                            $image_name = time() . "_" . md5($originalName) . '.jpg';
                            if (!file_exists($this->container->getParameter('path_img_album_original').$name_path))
                                mkdir($this->container->getParameter('path_img_album_original').$name_path, 0777, true);
                            if (!file_exists($this->container->getParameter('path_img_album_thums').$name_path))
                                mkdir($this->container->getParameter('path_img_album_thums').$year."/".$maonth."/".$day, 0777, true);
                            $path = $image->getPathname();
                            $image = $imagine->open($path);
                            $w = $image->getSize()->getWidth();
                            $h = $image->getSize()->getHeight();

                            if($w >= 1280 or $h >= 872){
                                if($w > $h){
                                    $count = $image->getSize()->getHeight() / 872;
                                    $width = $image->getSize()->getWidth() / $count;
                                    $image->resize(new Box($width, 872), ImageInterface::FILTER_LANCZOS);
                                }else{
                                    $count = $image->getSize()->getWidth() / 1280;
                                    $height = $image->getSize()->getHeight() / $count;
                                    $image->resize(new Box(1280, $height), ImageInterface::FILTER_LANCZOS);
                                }
                            }

                            $image->save($this->container->getParameter('path_img_album_original').$originalName);
                            rename($this->container->getParameter('path_img_album_original').$originalName,$this->container->getParameter('path_img_album_original').$name_path.$image_name);
                            if($w > $h){
                                $count = $image->getSize()->getHeight() / 178;
                                $width = $image->getSize()->getWidth() / $count;
                                $image->resize(new Box($width, 178), ImageInterface::FILTER_LANCZOS);
                            }else{
                                $count = $image->getSize()->getWidth() / 158;
                                $height = $image->getSize()->getHeight() / $count;
                                $image->resize(new Box(158, $height), ImageInterface::FILTER_LANCZOS);
                            }


                            $image->save($this->container->getParameter('path_img_album_thums').$originalName);
                            rename($this->container->getParameter('path_img_album_thums').$originalName,$this->container->getParameter('path_img_album_thums').$name_path.$image_name);

                            $im = new Images();
                            $im->setAlbum($album);
                            $im->setName($image_name);
                            $im->setPath($name_path);
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
                        $response = new Respon("Error type", 500);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                } else {
                    $response = new Respon("Error size", 500);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
            } else {
                $response = new Respon("Error UploadObject", 500);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
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

    public function eventTmpAction(){

        return $this->render('CreativerFrontBundle:Default:eventTmp.html.twig', array());
    }

    public function createEventTmpAction($date){

        $user = $this->get('security.context')->getToken()->getUser();

        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $response = new Response(null, 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')->findBy(array('user'=>$user,'isActive'=>0));

        if(!empty($event)){
            $event = $event[0];
        }

        if(empty($event)){
            $event = new Events();
            $event->setUser($user);
            $em->persist($event);
            $em->flush();
        }

        $event_id = $event->getId();

        return $this->render('CreativerFrontBundle:Default:createEventTmp.html.twig', array('event_id' => $event_id));
    }

    public function editEventTmpAction(){
        $user = $this->get('security.context')->getToken()->getUser();

        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $response = new Response(null, 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        return $this->render('CreativerFrontBundle:Default:editEventTmp.html.twig', array());
    }

    public function peopleTmpAction(){

        return $this->render('CreativerFrontBundle:Default:peopleTmp.html.twig', array());
    }

    public function searchEventsTmpAction(){

        return $this->render('CreativerFrontBundle:Default:searchEventsTmp.html.twig', array());
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

            $year = date("Y");
            $maonth = date("m");
            $day = date("d");
            $name_path = $year."/".$maonth."/".$day."/";


            $em = $this->getDoctrine()->getEntityManager();
            $PostBaraholka = $em->getRepository("CreativerFrontBundle:PostBaraholka")->find(array('id' => $post_id, 'user' => $user));
            $PostCategory = $em->getRepository("CreativerFrontBundle:PostCategory")->find($post_category);
            $PostCity = $em->getRepository("CreativerFrontBundle:PostCity")->find($city);
            $CategoriesBaraholka = $em->getRepository("CreativerFrontBundle:CategoriesBaraholka")->find($section);

            $PostBaraholka->setCategoriesBaraholka($CategoriesBaraholka);
            $PostBaraholka->setPostCategory($PostCategory);
            $PostBaraholka->setPostCity($PostCity);
            $PostBaraholka->setName($name);
            $PostBaraholka->setPath($name_path);
            $PostBaraholka->setDescription($description);
            $PostBaraholka->setFullDescription($full_description);
            $PostBaraholka->setPrice($full_price);
            $PostBaraholka->setIsActive(1);
            $PostBaraholka->setAuction($auction);


            if (($image instanceof UploadedFile) && ($image->getError() == '0')) {
                if (($image->getSize() < 10485760)) {
                    $originalName = $image->getClientOriginalName();
                    $file_type = $image->getMimeType();
                    $valid_filetypes = array('image/jpg', 'image/jpeg', 'image/bmp', 'image/png', 'image/gif');
                    if (in_array(strtolower($file_type), $valid_filetypes)) {

                        try {
                            $imagine = new \Imagine\Imagick\Imagine();
                            $name_path = $year."/".$maonth."/".$day."/";
                            $image_name = time() . "_" . md5($originalName) . '.jpg';
                            if (!file_exists($this->container->getParameter('path_img_baraholka_original').$name_path))
                                mkdir($this->container->getParameter('path_img_baraholka_original').$name_path, 0777, true);
                            if (!file_exists($this->container->getParameter('path_img_baraholka_thums').$name_path))
                                mkdir($this->container->getParameter('path_img_baraholka_thums').$year."/".$maonth."/".$day, 0777, true);
                            $path = $image->getPathname();
                            $image = $imagine->open($path);
                            $w = $image->getSize()->getWidth();
                            $h = $image->getSize()->getHeight();

                            if($w >= 800 or $h >= 600){
                                if($w > $h){
                                    $count = $image->getSize()->getHeight() / 600;
                                    $width = $image->getSize()->getWidth() / $count;
                                    $image->resize(new Box($width, 600), ImageInterface::FILTER_LANCZOS);
                                }else{
                                    $count = $image->getSize()->getWidth() / 800;
                                    $height = $image->getSize()->getHeight() / $count;
                                    $image->resize(new Box(800, $height), ImageInterface::FILTER_LANCZOS);
                                }
                            }

                            $image->save($this->container->getParameter('path_img_baraholka_original').$originalName);
                            rename($this->container->getParameter('path_img_baraholka_original').$originalName,$this->container->getParameter('path_img_baraholka_original').$name_path.$image_name);
                            if($w > $h){
                                $count = $image->getSize()->getHeight() / 178;
                                $width = $image->getSize()->getWidth() / $count;
                                $image->resize(new Box($width, 178), ImageInterface::FILTER_LANCZOS);
                            }else{
                                $count = $image->getSize()->getWidth() / 158;
                                $height = $image->getSize()->getHeight() / $count;
                                $image->resize(new Box(158, $height), ImageInterface::FILTER_LANCZOS);
                            }


                            $image->save($this->container->getParameter('path_img_baraholka_thums').$originalName);
                            rename($this->container->getParameter('path_img_baraholka_thums').$originalName,$this->container->getParameter('path_img_baraholka_thums').$name_path.$image_name);


                            if($main == 1){
                                $PostBaraholka->setImg($image_name);
                                $PostBaraholka->setPath($name_path);
                            }

                            $im = new ImagesBaraholka();
                            $im->setPostBaraholka($PostBaraholka);
                            $im->setName($image_name);
                            $im->setPath($name_path);
                            $em->persist($im);


                        } catch (\Imagine\Exception\Exception $e) {
                            die("error upload image ".$e);
                        }


                    } else {
                        $response = new Respon("Error type", 500);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                } else {
                    $response = new Respon("Error size", 500);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
            } else {
                $em->persist($PostBaraholka);
                $em->flush();
                $id = $PostBaraholka->getId();
                $array = array('id' => $id);
                $response = new Respon(json_encode($array), 200);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
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


    public function createShopTmpAction(){

        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $userId = $this->get('security.context')->getToken()->getUser()->getId();

        return $this->render('CreativerFrontBundle:Default:createShopTmp.html.twig');

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
                if (($image->getSize() < 10485760)) {
                    $originalName = $image->getClientOriginalName();
                    $file_type = $image->getMimeType();
                    $valid_filetypes = array('image/jpg', 'image/jpeg', 'image/bmp', 'image/png', 'image/gif');
                    if (in_array(strtolower($file_type), $valid_filetypes)) {


                        $year = date("Y");
                        $maonth = date("m");
                        $day = date("d");

                        try {
                            $imagine = new \Imagine\Imagick\Imagine();
                            $name_path = $year."/".$maonth."/".$day."/";
                            $image_name = time() . "_" . md5($originalName) . '.jpg';
                            if (!file_exists($this->container->getParameter('path_img_baraholka_original').$name_path))
                                mkdir($this->container->getParameter('path_img_baraholka_original').$name_path, 0777, true);
                            if (!file_exists($this->container->getParameter('path_img_baraholka_thums').$name_path))
                                mkdir($this->container->getParameter('path_img_baraholka_thums').$year."/".$maonth."/".$day, 0777, true);
                            $path = $image->getPathname();
                            $image = $imagine->open($path);
                            $w = $image->getSize()->getWidth();
                            $h = $image->getSize()->getHeight();

                            if($w >= 800 or $h >= 600){
                                if($w > $h){
                                    $count = $image->getSize()->getHeight() / 600;
                                    $width = $image->getSize()->getWidth() / $count;
                                    $image->resize(new Box($width, 600), ImageInterface::FILTER_LANCZOS);
                                }else{
                                    $count = $image->getSize()->getWidth() / 800;
                                    $height = $image->getSize()->getHeight() / $count;
                                    $image->resize(new Box(800, $height), ImageInterface::FILTER_LANCZOS);
                                }
                            }

                            $image->save($this->container->getParameter('path_img_baraholka_original').$originalName);
                            rename($this->container->getParameter('path_img_baraholka_original').$originalName,$this->container->getParameter('path_img_baraholka_original').$name_path.$image_name);
                            if($w > $h){
                                $count = $image->getSize()->getHeight() / 178;
                                $width = $image->getSize()->getWidth() / $count;
                                $image->resize(new Box($width, 178), ImageInterface::FILTER_LANCZOS);
                            }else{
                                $count = $image->getSize()->getWidth() / 158;
                                $height = $image->getSize()->getHeight() / $count;
                                $image->resize(new Box(158, $height), ImageInterface::FILTER_LANCZOS);
                            }


                            $image->save($this->container->getParameter('path_img_baraholka_thums').$originalName);
                            rename($this->container->getParameter('path_img_baraholka_thums').$originalName,$this->container->getParameter('path_img_baraholka_thums').$name_path.$image_name);


                            $im = new ImagesBaraholka();
                            $im->setName($image_name);
                            $im->setPath($name_path);
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
                        $response = new Respon("Error type", 500);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                } else {
                    $response = new Respon("Error size", 500);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
            } else {
                $response = new Respon("Error UploadObject", 500);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
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
                if (($image->getSize() < 10485760)) {
                    $originalName = $image->getClientOriginalName();
                    $file_type = $image->getMimeType();
                    $valid_filetypes = array('image/jpg', 'image/jpeg', 'image/bmp', 'image/png', 'image/gif');
                    if (in_array(strtolower($file_type), $valid_filetypes)) {
                        try {
                            $imagine = new \Imagine\Imagick\Imagine();

                            $year = date("Y");
                            $maonth = date("m");
                            $day = date("d");

                            $name_path = $year."/".$maonth."/".$day."/";
                            $image_name = time() . "_" . md5($originalName) . '.jpg';
                            if (!file_exists($this->container->getParameter('path_img_post_original').$name_path))
                                mkdir($this->container->getParameter('path_img_post_original').$name_path, 0777, true);
                            if (!file_exists($this->container->getParameter('path_img_post_thums').$name_path))
                                mkdir($this->container->getParameter('path_img_post_thums').$year."/".$maonth."/".$day, 0777, true);
                            $path = $image->getPathname();
                            $image = $imagine->open($path);
                            $w = $image->getSize()->getWidth();
                            $h = $image->getSize()->getHeight();

                            if($w >= 800 or $h >= 600){
                                if($w > $h){
                                    $count = $image->getSize()->getHeight() / 600;
                                    $width = $image->getSize()->getWidth() / $count;
                                    $image->resize(new Box($width, 600), ImageInterface::FILTER_LANCZOS);
                                }else{
                                    $count = $image->getSize()->getWidth() / 800;
                                    $height = $image->getSize()->getHeight() / $count;
                                    $image->resize(new Box(800, $height), ImageInterface::FILTER_LANCZOS);
                                }
                            }

                            $image->save($this->container->getParameter('path_img_post_original').$originalName);
                            rename($this->container->getParameter('path_img_post_original').$originalName,$this->container->getParameter('path_img_post_original').$name_path.$image_name);
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


                            $image->save($this->container->getParameter('path_img_post_thums').$originalName);
                            rename($this->container->getParameter('path_img_post_thums').$originalName,$this->container->getParameter('path_img_post_thums').$name_path.$image_name);



                            $im = new PostImages();
                            $im->setName($image_name);
                            $im->setPath($name_path);
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
                        $response = new Respon("Error type", 500);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                } else {
                    $response = new Respon("Error size", 500);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
            } else {
                $response = new Respon("Error UploadObject", 500);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }

            $response = new Respon(null, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }


    public function savePostDocumentsAction(){

        $user = $this->get('security.context')->getToken()->getUser();

        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $response = new Response(null, 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $file = $request->files->get('file');
            $post_id = $request->get('post_id');

            $em = $this->getDoctrine()->getEntityManager();
            $Post = $em->getRepository("CreativerFrontBundle:Posts")->find(array('id' => $post_id, 'user' => $user));


            if (($file instanceof UploadedFile) && ($file->getError() == '0')) {
                $size = $file->getSize();
                if (($size < 10485760)) {
                    $originalName = $file->getClientOriginalName();
                    $file_type = $file->getMimeType();

                    $valid_filetypes = array('application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                             'text/plain',
                                             '',
                                             'image/png',
                                             'image/gif',
                                             'inode/x-empty',
                                             'application/vnd.ms-powerpoint',
                                             'application/octet-stream',
                                             'application/vnd.ms-excel',
                                             'application/msword');


                    $format = explode(".", $originalName)[1];

                    if (in_array(strtolower($file_type), $valid_filetypes) and !empty($format)) {
                        try {
                            $year = date("Y");
                            $maonth = date("m");
                            $day = date("d");
                            $name_path = $year."/".$maonth."/".$day."/";
                            $file_name = time() . "_" . md5($originalName).'.'.$format;

                            if (!file_exists($this->container->getParameter('path_documents').$name_path))
                                mkdir($this->container->getParameter('path_documents').$name_path, 0777, true);
                            $path = $file->getPathname();


                            //$image->save($this->container->getParameter('path_documents').$originalName);
                            rename($path,$this->container->getParameter('path_documents').$name_path.$file_name);



                            $im = new PostDocuments();
                            $im->setName($file_name);
                            $im->setPath($name_path);
                            $im->setRealName($originalName);
                            $im->setSize($size);
                            $Post->addPostDocument($im);
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
                        $response = new Respon("Error type", 500);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                } else {
                    $response = new Respon("Error size", 500);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
            } else {
                $response = new Respon("Error UploadObject", 500);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }

            $response = new Respon(null, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    public function followingTmpAction($id)
    {
        return $this->render('CreativerFrontBundle:Default:followingTmp.html.twig', array('id' => $id));
    }

    public function followersTmpAction($id)
    {
        return $this->render('CreativerFrontBundle:Default:followersTmp.html.twig', array('id' => $id));
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

    public function settingsTmpAction(){

        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $userId = $this->get('security.context')->getToken()->getUser()->getId();
        $tariffs = $this->getDoctrine()->getRepository('CreativerFrontBundle:Tariffs')->findAll();


        return $this->render('CreativerFrontBundle:Default:settingsTmp.html.twig', array('id' => $userId, 'tariffs' => $tariffs));
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

    public function showPhotoTmpAction(){


        return $this->render('CreativerFrontBundle:Default:show_photoTmp.html.twig');
    }

    public function showProductPhotoTmpAction(){


        return $this->render('CreativerFrontBundle:Default:show_product_photoTmp.html.twig');
    }

    public function showPostPhotoTmpAction(){


        return $this->render('CreativerFrontBundle:Default:show_post_photoTmp.html.twig');
    }

    public function cacheAction(){


        return $this->render('CreativerFrontBundle::cache_manifest.html.twig');
    }

    public function shopsTmpAction(){


        return $this->render('CreativerFrontBundle:Default:shopsTmp.html.twig');
    }

    public function shopTmpAction(){


        return $this->render('CreativerFrontBundle:Default:shopTmp.html.twig');
    }

    public function editShopTmpAction(){


        return $this->render('CreativerFrontBundle:Default:editShopTmp.html.twig');
    }

    public function forgotPasswordAction(){


        if ($this->get('request')->getMethod() == 'POST') {
            $email = $this->get('request')->get('email');

            $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findBy(array('email'=>$email));

            if(!$user){
                return $this->render('CreativerFrontBundle:Default:mistake_forgot_password.html.twig', array('email' => $email));
            }else{
                $real_password = $user[0]->getRealPassword();

                $mailer = $this->get('swiftmailer.mailer');
                $message = \Swift_Message::newInstance()
                    ->setSubject('Восстановление пароля')
                    ->setFrom(array('info@creativer.by' => 'Creativer'))
                    ->setTo($email)
                    ->setContentType("text/html")
                    ->setBody($this->renderView('CreativerFrontBundle:Default:letter_forgot_password.html.twig', array('real_password' => $real_password)));
                $mailer->send($message);
            }

            return $this->render('CreativerFrontBundle:Default:forgot_password.html.twig', array('form' => false));
        }else{
            return $this->render('CreativerFrontBundle:Default:forgot_password.html.twig', array('form' => true));
        }
    }

    public function uploadImageEventAction(){

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $image = $request->files->get('file');

            if (($image instanceof UploadedFile) && ($image->getError() == '0')) {
                if (($image->getSize() < 10485760)) {
                    $originalName = $image->getClientOriginalName();
                    $file_type = $image->getMimeType();
                    $valid_filetypes = array('image/jpg', 'image/jpeg', 'image/bmp', 'image/png', 'image/gif');
                    if (in_array(strtolower($file_type), $valid_filetypes)) {
                        //Start Uploading File
                        $user = $this->get('security.context')->getToken()->getUser();
                        $em = $this->getDoctrine()->getEntityManager();

                        $id = $this->get('request')->request->get('id');
                        if($id){
                            $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')->find($id);
                            $path_img_event_original = $this->container->getParameter('path_img_event_original');
                            $fs = new Filesystem();
                            $fs->remove(array($path_img_event_original.$event->getPath().$event->getImg()));
                        }else{
                            $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')->findBy(array('user'=>$user,'isActive'=>0))[0];
                        }
                        try {
                            $imagine = new \Imagine\Imagick\Imagine();

                            $year = date("Y");
                            $maonth = date("m");
                            $day = date("d");

                            $name_path = $year."/".$maonth."/".$day."/";
                            $image_name = time() . "_" . md5($originalName) . '.jpg';
                            if (!file_exists($this->container->getParameter('path_img_event_original').$name_path))
                                mkdir($this->container->getParameter('path_img_event_original').$name_path, 0777, true);
                            $path = $image->getPathname();
                            $image = $imagine->open($path);
                            $w = $image->getSize()->getWidth();
                            $h = $image->getSize()->getHeight();

                            if($w >= 800 or $h >= 600){
                                if($w > $h){
                                    $count = $image->getSize()->getHeight() / 600;
                                    $width = $image->getSize()->getWidth() / $count;
                                    $image->resize(new Box($width, 600), ImageInterface::FILTER_LANCZOS);
                                }else{
                                    $count = $image->getSize()->getWidth() / 600;
                                    $height = $image->getSize()->getHeight() / $count;
                                    $image->resize(new Box(600, $height), ImageInterface::FILTER_LANCZOS);
                                }
                            }

                            $image->save($this->container->getParameter('path_img_event_original').$originalName);
                            rename($this->container->getParameter('path_img_event_original').$originalName,$this->container->getParameter('path_img_event_original').$name_path.$image_name);


                            $event->setImg($image_name);
                            $event->setPath($name_path);
                            $em->persist($event);
                            $em->flush();

                            $serializer = $this->container->get('jms_serializer');
                            $event = $serializer
                                ->serialize(
                                    $event,
                                    'json',
                                    SerializationContext::create()
                                        ->setGroups(array('getEvent'))
                                );

                            $response = new Respon($event);
                            $response->headers->set('Content-Type', 'application/json');
                            return $response;
                        } catch (\Imagine\Exception\Exception $e) {
                            die("error upload image ".$e);
                        }

                    } else {
                        $response = new Respon("Error type", 500);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                } else {
                    $response = new Respon("Error size", 500);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
            } else {
                $response = new Respon("Error UploadObject", 500);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }

            $response = new Respon($message, 500);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            return $this->render('CreativerFrontBundle:Default:createEventTmp.html.twig');
        }

        return $this->render('CreativerFrontBundle:Default:createEventTmp.html.twig');
    }

    public function uploadShopAction(){

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $image = $request->files->get('file');
            $title = $request->get('title');
            $description = $request->get('description');
            $site = $request->get('site');
            $address_shop = $request->get('address');
            $telephone = $request->get('telephone');
            $email = $request->get('email');
            $full_description = $request->get('full_description');
            $stop = $request->get('stop');
            $main = $request->get('main');
            $selectCategories = $request->get('selectCategories');
            $selectCategories = explode(",", $selectCategories);


            if (($image instanceof UploadedFile) && ($image->getError() == '0')) {
                if (($image->getSize() < 10485760)) {
                    $originalName = $image->getClientOriginalName();
                    $file_type = $image->getMimeType();
                    $valid_filetypes = array('image/jpg', 'image/jpeg', 'image/bmp', 'image/png', 'image/gif');
                    if (in_array(strtolower($file_type), $valid_filetypes)) {
                        //Start Uploading File
                        $userId = $this->get('security.context')->getToken()->getUser()->getId();
                        $em = $this->getDoctrine()->getEntityManager();
                        $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findBy(array('id'=>$userId));
                        $shop= $em->getRepository("CreativerFrontBundle:Shops")->findBy(array('isActive' => 0));
                        $categories = $em->getRepository("CreativerFrontBundle:Categories")->findById($selectCategories);

                        $address_shop = json_decode($address_shop);


                        if(empty($shop)){
                            $shop = new Shops();
                            $shop->setName($title);
                            $shop->setEmail($email);
                            $shop->setTelephone($telephone);
                            $shop->setSite($site);
                            $shop->setDescription($description);
                            $shop->setFullDescription($full_description);
                            foreach($categories as $cat){
                                $shop->addCategory($cat);
                            }
                            foreach($address_shop as $adr){
                                $address = new Address();
                                $address->setAddress($adr);
                                $shop->addAddress($address);
                                $address->setShop($shop);
                                $em->persist($address);
                            }
                        }else{
                            $shop = $shop[0];
                        }

                        if($stop == 1){
                            $shop->setIsActive(1);
                        }

                        $year = date("Y");
                        $maonth = date("m");
                        $day = date("d");


                        try {
                            $imagine = new \Imagine\Imagick\Imagine();
                            $name_path = $year."/".$maonth."/".$day."/";
                            $image_name = time() . "_" . md5($originalName) . '.jpg';
                            if (!file_exists($this->container->getParameter('path_img_shop').$name_path))
                                mkdir($this->container->getParameter('path_img_shop').$name_path, 0777, true);
                            $path = $image->getPathname();
                            $image = $imagine->open($path);
                            $w = $image->getSize()->getWidth();
                            $h = $image->getSize()->getHeight();

                            if($w >= 600 or $h >= 400){
                                if($w > $h){
                                    $count = $image->getSize()->getHeight() / 400;
                                    $width = $image->getSize()->getWidth() / $count;
                                    $image->resize(new Box($width, 400), ImageInterface::FILTER_LANCZOS);
                                }else{
                                    $count = $image->getSize()->getWidth() / 600;
                                    $height = $image->getSize()->getHeight() / $count;
                                    $image->resize(new Box(600, $height), ImageInterface::FILTER_LANCZOS);
                                }
                            }

                            $image->save($this->container->getParameter('path_img_shop').$originalName);
                            rename($this->container->getParameter('path_img_shop').$originalName,$this->container->getParameter('path_img_shop').$name_path.$image_name);

                            $images_shops = new  ImagesShops();
                            $images_shops->setName($image_name);
                            $images_shops->setPath($name_path);


                            $shop->addImage($images_shops);
                            $images_shops->setShop($shop);


                            if($main == 1){
                                $shop->setImg($image_name);
                                $shop->setPath($name_path);
                            }
                            $em->persist($shop);
                            $em->persist($images_shops);
                            $em->flush();

                        } catch (\Imagine\Exception\Exception $e) {
                            die("error upload image ".$e);
                        }

                    } else {
                        $response = new Respon("Error type", 500);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                } else {
                    $response = new Respon("Error size", 500);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
            } else {
                $response = new Respon("Error UploadObject", 500);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $id = $shop->getId();
            $array = array('id' => $id);
            $response = new Respon(json_encode($array), 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            return $this->render('CreativerFrontBundle:Default:createShopTmp.html.twig');
        }

        return $this->render('CreativerFrontBundle:Default:createShopTmp.html.twig');
    }



    public function editImagesShopAction(){

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
            $shops = $em->getRepository("CreativerFrontBundle:Shops")->find(array('id' => $id));


            if (($image instanceof UploadedFile) && ($image->getError() == '0')) {
                if (($image->getSize() < 10485760)) {
                    $originalName = $image->getClientOriginalName();
                    $file_type = $image->getMimeType();
                    $valid_filetypes = array('image/jpg', 'image/jpeg', 'image/bmp', 'image/png', 'image/gif');
                    if (in_array(strtolower($file_type), $valid_filetypes)) {


                        $year = date("Y");
                        $maonth = date("m");
                        $day = date("d");

                        try {
                            $imagine = new \Imagine\Imagick\Imagine();
                            $name_path = $year."/".$maonth."/".$day."/";
                            $image_name = time() . "_" . md5($originalName) . '.jpg';
                            if (!file_exists($this->container->getParameter('path_img_shop').$name_path))
                                mkdir($this->container->getParameter('path_img_shop').$name_path, 0777, true);
                            $path = $image->getPathname();
                            $image = $imagine->open($path);
                            $w = $image->getSize()->getWidth();
                            $h = $image->getSize()->getHeight();

                            if($w >= 600 or $h >= 400){
                                if($w > $h){
                                    $count = $image->getSize()->getHeight() / 400;
                                    $width = $image->getSize()->getWidth() / $count;
                                    $image->resize(new Box($width, 400), ImageInterface::FILTER_LANCZOS);
                                }else{
                                    $count = $image->getSize()->getWidth() / 600;
                                    $height = $image->getSize()->getHeight() / $count;
                                    $image->resize(new Box(600, $height), ImageInterface::FILTER_LANCZOS);
                                }
                            }

                            $image->save($this->container->getParameter('path_img_shop').$originalName);
                            rename($this->container->getParameter('path_img_shop').$originalName,$this->container->getParameter('path_img_shop').$name_path.$image_name);


                            $im = new ImagesShops();
                            $im->setName($image_name);
                            $im->setPath($name_path);
                            $shops->addImage($im);
                            $im->setShop($shops);
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
                        $response = new Respon("Error type", 500);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                } else {
                    $response = new Respon("Error size", 500);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
            } else {
                $response = new Respon("Error UploadObject", 500);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }

            $response = new Respon(null, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

    }

    public function rateAction(){
        return $this->render('CreativerFrontBundle:Default:rate.html.twig', array());
    }

    public function rulesAction(){
        return $this->render('CreativerFrontBundle:Default:rules.html.twig', array());
    }

    public function agreementAction(){
        return $this->render('CreativerFrontBundle:Default:agreement.html.twig', array());
    }

    public function albumSocialRedirectAction($id,$name){

        $image = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')->findBy(array('name'=>$name))[0];

        $id = $image->getId();
        $name = $image->getName();
        $id_album = $image->getAlbum()->getId();
        $path = $image->getPath();
        $text = $image->getText();

        return $this->render('CreativerFrontBundle::album_social_redirect.html.twig', array(
            'id' => $id,
            'name' => $name,
            'id_album' => $id_album,
            'path' => $path,
            'text' => $text));
    }

    public function eventRedirectAction($id){

        $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')->findBy(array('id'=>$id))[0];

        $id = $event->getId();
        $name = $event->getName();
        $path = $event->getPath();
        $img = $event->getImg();

        return $this->render('CreativerFrontBundle::event_redirect.html.twig', array(
            'id' => $id,
            'name' => $name,
            'path' => $path,
            'img' => $img));
    }

    public function userRedirectAction($id){

        $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findBy(array('id'=>$id))[0];

        $id = $event->getId();
        $name = $event->getUsername();
        $lastname = $event->getLastname();
        $info = $event->getInfo();
        $avatar = $event->getAvatar();

        return $this->render('CreativerFrontBundle::user_redirect.html.twig', array(
            'id' => $id,
            'name' => $name,
            'lastname' => $lastname,
            'info' => $info,
            'avatar' => $avatar
            ));
    }
}
