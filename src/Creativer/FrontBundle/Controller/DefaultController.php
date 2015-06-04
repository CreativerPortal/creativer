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
use Creativer\FrontBundle\Entity\Avatar;
use Creativer\FrontBundle\Entity\Register;
use Creativer\FrontBundle\Services\ImageServices;
use Imagine\Image\Box;
use Imagine\Imagick;
use Imagine\Image\ImageInterface;



class DefaultController extends Controller
{

    public function registrationAction()
    {
        $request = $this->get('request');

        $register = new Register();

        $form = $this->createFormBuilder($register)
            ->add('username', 'text')
            ->add('lastname', 'text')
            ->add('password', 'repeated', array('type' => 'password', 'invalid_message' => 'Ваш пароль не совпадает',))
            ->add('email', 'email')
            ->add('img', 'hidden')
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
            $avatar = new Avatar();
            $wall = new Wall();
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($request->get('form')['password']['first'], $user->getSalt());
            $avatar->setImg($img);
            $user->setUsername($request->get('form')['username']);
            $user->setLastname($request->get('form')['lastname']);
            $user->setEmail($request->get('form')['email']);
            $user->setAvatar($avatar);
            $user->setPassword($password);
            $user->setWall($wall);
            $wall->setUser($user);


            $em = $this->getDoctrine()->getManager();
            $role = $em->getRepository('CreativerFrontBundle:Role')->findOneById(2);
            $user->addRole($role);

            $em->persist($avatar);
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

        return $this->render('CreativerFrontBundle:Default:createAlbumTmp.html.twig');
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


//                        $document = new Document();
//                        $document->setFile($image);
//                        $document->setSubDirectory('uploads');
//                        $document->processFile();
//                        $uploadedURL=$uploadedURL = $document->getUploadDirectory() . DIRECTORY_SEPARATOR . $document->getSubDirectory() . DIRECTORY_SEPARATOR . $image->getBasename();
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
            $response = new Response();
            return $response->setStatusCode(200);
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

    public function albumTmpAction(){

        return $this->render('CreativerFrontBundle:Default:albumTmp.html.twig', array());
    }

    public function favoritTmpAction(){

        return $this->render('CreativerFrontBundle:Default:favoritTmp.html.twig', array());
    }

    public function productsTmpAction(){

        return $this->render('CreativerFrontBundle:Default:productsTmp.html.twig', array());
    }

    public function servicesTmpAction(){


        return $this->render('CreativerFrontBundle:Default:servicesTmp.html.twig', array());
    }

    public function feedbackAction(){


        return $this->render('CreativerFrontBundle:Default:feedback.html.twig', array());
    }

    public function fleamarketAction(){


        return $this->render('CreativerFrontBundle:Default:fleamarket.html.twig', array());
    }

    public function messagesAction(){


        return $this->render('CreativerFrontBundle:Default:messages.html.twig', array());
    }

    public function redisAction(){

        $redis = $this->get('snc_redis.default');


        $redis->sadd(12, 'qwe');

        return $this->render('CreativerFrontBundle:Default:messages.html.twig', array());
    }

}
