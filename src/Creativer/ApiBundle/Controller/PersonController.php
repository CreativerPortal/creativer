<?php

namespace Creativer\ApiBundle\Controller;

use Blameable\Fixture\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Creativer\FrontBundle\Entity\User;
use Creativer\FrontBundle\Entity\Wall;
use Creativer\FrontBundle\Entity\Posts;
use Creativer\FrontBundle\Entity\PostVideos;
use Creativer\FrontBundle\Entity\Comments;
use Creativer\FrontBundle\Entity\Tariffs;
use Creativer\FrontBundle\Entity\ImageComments;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Filesystem\Filesystem;
use Creativer\FrontBundle\Services\ImageServices;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response as Respon;
use Symfony\Component\HttpFoundation\RedirectResponse;



class PersonController extends Controller
{

    /**
     * @return array
     * @Post("/v1/save_post")
     * @View(serializerGroups={"getPost"})
     */
    public function savePostAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $data = json_decode($this->get("request")->getContent());
        $user = $this->get('security.context')->getToken()->getUser();

        $wall = $this->getDoctrine()->getRepository('CreativerFrontBundle:Wall')->findOneById($data->wall_id);


        $post = new Posts();
        $em = $this->getDoctrine()->getManager();

        $post->setUser($user)
            ->setWall($wall);

        if(!empty($data->text)){
            $post->setText($data->text);
        }else{
            $post->setText(' ');
        }

        if(!empty($data->videos[0])){
            foreach($data->videos as $key=>$val){
                $video = new PostVideos();
                $video->setUrl($val);
                $video->setPost($post);
                $em->persist($video);
            }
        }

        $em->persist($post);
        $em->flush();

        return array('post' => $post);
    }

    /**
     * @return array
     * @Post("/v1/save_comment")
     * @View(serializerGroups={"getComments"})
     */
    public function saveCommentAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $data = json_decode($this->get("request")->getContent());
        $user = $this->get('security.context')->getToken()->getUser();
        $post = $this->getDoctrine()->getRepository('CreativerFrontBundle:Posts')->findOneById($data->post_id);


        $comment = new Comments();
        $comment->setText($data->text)
            ->setPost($post)
            ->setUser($user);


        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        return array('comment' => $comment);
    }

    /**
     * @return array
     * @Post("/v1/remove_comment")
     * @View()
     */
    public function removeCommentAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $comment_id = $this->get('request')->request->get('id');
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $comment = $this->getDoctrine()->getRepository('CreativerFrontBundle:Comments')->find($comment_id);

        $em->remove($comment);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/get_album_comments")
     * @View(serializerGroups={"getAlbumComments"})
     */
    public function getAlbumCommentsAction()
    {
        $data = json_decode($this->get("request")->getContent());
        $album = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')->findOneById($data->id_album);

        return array('images' => $album->getImages());
    }

    /**
     * @return array
     * @Post("/v1/get_album_by_id")
     * @View(serializerGroups={"getAlbumById"})
     */
    public function getAlbumByIdAction()
    {
        $data = json_decode($this->get("request")->getContent());
        $album = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')->findOneById($data->id_album);

        return array('album' => $album);
    }

    /**
     * @return array
     * @Post("/v1/save_image_comments")
     * @View(serializerGroups={"getAlbumComments"})
     */
    public function saveImageCommentsAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $data = json_decode($this->get("request")->getContent());
        $user = $this->get('security.context')->getToken()->getUser();
       // die(\Doctrine\Common\Util\Debug::dump($avatar));
        $image = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')->findOneById($data->image_id);

        if($image->getAlbum()->getUser()->getId() != $this->get('security.context')->getToken()->getUser()->getid()){
            $image->setViewed(false);
        }

        $imageComment = new ImageComments();

        $imageComment->setUser($user)
            ->setImage($image)
            ->setText($data->text);

        $em = $this->getDoctrine()->getManager();
        $em->persist($image);
        $em->persist($imageComment);
        $em->flush();


        if($image){
            $persister = $this->get('fos_elastica.object_persister.app.images');
            $persister->replaceOne($image);
        }

        return $imageComment;
    }

    /**
     * @Post("/v1/get_user")
     * @View(serializerGroups={"getUser"})
     */
    public function getUserAction()
    {
        if(!$this->get('request')->request->get('id'))
        {
            $id = $this->get('security.context')->getToken()->getUser()->getId();
        }else{
            $id = $this->get('request')->request->get('id');
        }
        $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findBy(array('id'=>$id))[0];

        $posts = $user->getWall()->getPosts()->slice(0, 5);

        $user = array('user' => $user, 'posts' => $posts);

        $serializer = $this->container->get('jms_serializer');
        $response = $serializer
            ->serialize(
                $user,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
                    ->setGroups(array('getUser'))
            );

        $response = new Respon($response);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Post("/v1/get_news")
     * @View(serializerGroups={"getNews"})
     */
    public function getNewsAction()
    {
        if(!$this->get('request')->request->get('id'))
        {
            $id = $this->get('security.context')->getToken()->getUser()->getId();
        }else{
            $id = $this->get('request')->request->get('id');
        }
        //$user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findBy(array('id'=>$id))[0];

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQueryBuilder();
        $query
            ->select('e')
            ->from('CreativerFrontBundle:Posts', 'e')
            ->leftJoin('e.wall', 'wall')
            ->leftJoin('wall.user', 'user')
            ->leftJoin('user.favoritsWithMe', 'favoritsWithMe')
            ->orderBy("e.date", 'DESC')
            ->where('favoritsWithMe.id = :id')
            ->setParameter('id', $id);
        $results = $query->getQuery()->getResult();


        $serializer = $this->container->get('jms_serializer');
        $response = $serializer
            ->serialize(
                $results,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
                    ->setGroups(array('getUser'))
            );

        $response = new Respon($response);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Post("/v1/previous_posts")
     * @View(serializerGroups={"getUser"})
     */
    public function previousPostsAction()
    {
        if(!$this->get('request')->request->get('id'))
        {
            $id = $this->get('security.context')->getToken()->getUser()->getId();
        }else{
            $id = $this->get('request')->request->get('id');
        }
        $offset = $this->get('request')->request->get('offset');

        $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findBy(array('id'=>$id))[0];

        $posts = $user->getWall()->getPosts()->slice($offset, 5);


        $posts = array('posts' => $posts);

        $serializer = $this->container->get('jms_serializer');
        $response = $serializer
            ->serialize(
                $posts,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
                    ->setGroups(array('getUser'))
            );

        $response = new Respon($response);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Post("/v1/replace_password")
     * @View(serializerGroups={"getUser"})
     */
    public function replacePasswordAction()
    {
        $oldPassword = $this->get('request')->request->get('oldPassword');
        $newPassword = $this->get('request')->request->get('newPassword');
        $repeatPassword = $this->get('request')->request->get('repeatPassword');
        $realPassword = $this->get('security.context')->getToken()->getUser()->getRealPassword();

        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($this->get('security.context')->getToken()->getUser());
        $lastPassword = $encoder->encodePassword($newPassword, $this->get('security.context')->getToken()->getUser()->getSalt());


        if($oldPassword == $realPassword and $newPassword == $repeatPassword){
            $this->get('security.context')->getToken()->getUser()->setPassword($lastPassword);
            $this->get('security.context')->getToken()->getUser()->setRealPassword($newPassword);

            $em = $this->getDoctrine()->getManager();
            $em->persist($this->get('security.context')->getToken()->getUser());
            $em->flush();

            $array = array('success' => true);
            $response = new Respon(json_encode($array), 200);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }else{
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 400);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
    }

    /**
     * @Post("/v1/save_name")
     * @View(serializerGroups={"getUser"})
     */
    public function saveNameAction()
    {
        $userName = $this->get('request')->request->get('username');
        $lastName = $this->get('request')->request->get('lastname');

        if($userName and $lastName){
            $this->get('security.context')->getToken()->getUser()->setUsername($userName);
            $this->get('security.context')->getToken()->getUser()->setLastname($lastName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($this->get('security.context')->getToken()->getUser());
            $em->flush();

            $persister = $this->get('fos_elastica.object_persister.app.images');

            $id = $this->get('security.context')->getToken()->getUser();
            $images = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')
                ->createQueryBuilder('e')
                ->leftJoin('e.album', 'album')
                ->leftJoin('album.user', 'user')
                ->where('user.id = :items')
                ->setParameter('items', $id)
                ->getQuery()
                ->getResult();

            if($images){
                $persister->replaceMany($images);
            }

            $array = array('success' => true);
            $response = new Respon(json_encode($array), 200);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }else{
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 400);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
    }

    /**
     * @Post("/v1/change_auto_scroll")
     * @View(serializerGroups={"getUser"})
     */
    public function changeAutoScrollAction()
    {
        $autoscroll = $this->get('request')->request->get('autoscroll');

        $this->get('security.context')->getToken()->getUser()->setAutoscroll($autoscroll);

        $em = $this->getDoctrine()->getManager();
        $em->persist($this->get('security.context')->getToken()->getUser());
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Post("/v1/notification_message")
     * @View(serializerGroups={"getUser"})
     */
    public function notificationMessageAction()
    {
        $notification = $this->get('request')->request->get('notification_message');

        $this->get('security.context')->getToken()->getUser()->setNotificationMessage($notification);

        $em = $this->getDoctrine()->getManager();
        $em->persist($this->get('security.context')->getToken()->getUser());
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Post("/v1/notification_comment")
     * @View(serializerGroups={"getUser"})
     */
    public function notificationCommentAction()
    {
        $notification = $this->get('request')->request->get('notification_comment');

        $this->get('security.context')->getToken()->getUser()->setNotificationComment($notification);

        $em = $this->getDoctrine()->getManager();
        $em->persist($this->get('security.context')->getToken()->getUser());
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/save_field")
     * @View()
     */
    public function saveFieldAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getManager();
        $metadata = $em->getMetadataFactory()->getMetadataFor('Creativer\FrontBundle\Entity\User');
        $field = json_decode($this->get("request")->getContent());

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findOneById($id);


        foreach($field as $key => $val){
            $metadata->setFieldValue($user, $key, $val);
        }

        $em->flush();

        $posts = $user->getWall()->getPosts()->slice(0, 5);

        $user = array('user' => $user, 'posts' => $posts);

        $serializer = $this->container->get('jms_serializer');
        $response = $serializer
            ->serialize(
                $user,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
                    ->setGroups(array('getUser'))
            );

        $response = new Respon($response);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @return array
     * @Post("/v1/get_user_by_album_id")
     * @View(serializerGroups={"getUser"})
     */
    public function getUserByAlbumIdAction()
    {
        $id = $this->get('request')->request->get('id');
        $data = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')->find($id);
        $user = $data->getUser();

        $user = array('user' => $user);

        $serializer = $this->container->get('jms_serializer');
        $response = $serializer
            ->serialize(
                $user,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
                    ->setGroups(array('getUser'))
            );

        $response = new Respon($response);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @return array
     * @Post("/v1/delete_album")
     * @View()
     */
    public function deleteAlbumAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $id = $this->get('request')->request->get('id');

        $path_img_album_thums = $this->container->getParameter('path_img_album_thums');
        $path_img_album_original = $this->container->getParameter('path_img_album_original');


        $album = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')->find($id);
        $images = $album->getImages();


        $fs = new Filesystem();
        foreach($images as $key=>$val){
            $path = $val->getPath();
            $name = $val->getName();
            if(file_exists($path_img_album_thums.$path.$name) && !empty($path) && !empty($name)){
                $fs->remove(array($path_img_album_thums.$path.$name));
                $fs->remove(array($path_img_album_original.$path.$name));
                $em->remove($val);
            }
        }

        $em->remove($album);
        $em->flush();


        $array = array('success' => false);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/remove_image")
     * @View()
     */
    public function removeImageAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $id = $this->get('request')->request->get('id');

        $path_img_album_thums = $this->container->getParameter('path_img_album_thums');
        $path_img_album_original = $this->container->getParameter('path_img_album_original');


        $images = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')->find($id);

        $path = $images->getPath();
        $name = $images->getName();

        $fs = new Filesystem();
            if(file_exists($path_img_album_thums.$path.$name) && !empty($path) && !empty($name)){
                $fs->remove(array($path_img_album_thums.$path.$name));
                $fs->remove(array($path_img_album_original.$path.$name));
                $em->remove($images);
            }

        $em->remove($images);
        $em->flush();


        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/main_image")
     * @View()
     */
    public function mainImageAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $id = $this->get('request')->request->get('id');


        $images = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')->find($id);
        $album = $images->getAlbum();
        $album->setImg($images->getPath().$images->getName());


        $em->flush();
        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/delete_image")
     * @View(serializerGroups={"idUserByIdImage"})
     */
    public function deleteImageAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $image_id = $this->get('request')->request->get('image_id');
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $image = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')->findBy(array('id'=>$image_id))[0];

        $path_img_post_thums = $this->container->getParameter('path_img_post_thums');
        $path_img_post_original = $this->container->getParameter('path_img_post_original');

        if($image->getName() && $id == $image->getAlbum()->getUser()->getId()){
            $fs = new Filesystem();
            $name = $image->getName();
            $path = $image->getPath();
            if(file_exists($path_img_post_thums.$path.$name) && !empty($name) && !empty($path)){
                $fs->remove(array($path_img_post_thums.$path.$name));
                $fs->remove(array($path_img_post_original.$path.$name));
            }
            $em->remove($image);
        }

        $em->flush();


        return array('image' => $image);
    }

    /**
     * @return array
     * @Post("/v1/remove_post")
     * @View(serializerGroups={"getUser"})
     */
    public function removePostAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $post_id = $this->get('request')->request->get('post_id');
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $post = $this->getDoctrine()->getRepository('CreativerFrontBundle:Posts')->find($post_id);


        $path_img_post_thums = $this->container->getParameter('path_img_post_thums');
        $path_img_post_original = $this->container->getParameter('path_img_post_original');
        $path_documents = $this->container->getParameter('path_documents');


        $images = $post->getPostImages();
        $documents = $post->getPostDocuments();
        $comments = $post->getComments();
        $videos = $post->getPostVideos();

        if(!empty($videos)){
            foreach($videos as $key=>$val){
                $em->remove($val);
            }
        }

        if(!empty($comments)){
            foreach($comments as $key=>$val){
                $em->remove($val);
            }
        }

        if(!empty($images)){
            $fs = new Filesystem();
            foreach($images as $key=>$val){
                $path = $val->getPath();
                $name = $val->getName();
                if(file_exists($path_img_post_thums.$path.$name && !empty($path) && !empty($name))){
                    $fs->remove(array($path_img_post_thums.$path.$name));
                    $fs->remove(array($path_img_post_original.$path.$name));
                }
                $em->remove($val);
            }
        }

        if(!empty($documents)){
            $fs = new Filesystem();
            foreach($documents as $key=>$val){
                $path = $val->getPath();
                $name = $val->getName();
                if(file_exists($path_documents.$path.$name && !empty($path) && !empty($name))) {
                    $fs->remove(array($path_documents.$path.$name));
                    $em->remove($val);
                }
            }
        }

        $em->remove($post);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/add_favorits")
     * @View(serializerGroups={"getUser"})
     */
    public function addFavoritsAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getManager();
        $data = json_decode($this->get("request")->getContent());
        $newFriend = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findOneById($data->id);

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findOneById($id);

        $user->addMyFavorit($newFriend);

        $em->flush();



        $serializer = $this->container->get('jms_serializer');
        $response = $serializer
            ->serialize(
                $newFriend,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
                    ->setSerializeNull(true)
            );


        $response = new Respon($response, 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/remove_favorits")
     * @View(serializerGroups={"getUser"})
     */
    public function removeFavoritsAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getManager();
        $data = json_decode($this->get("request")->getContent());
        $oldFriend = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findOneById($data->id);

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findOneById($id);

        $user->removeMyFavorit($oldFriend);

        $em->flush();

        $serializer = $this->container->get('jms_serializer');
        $response = $serializer
            ->serialize(
                $oldFriend,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
                    ->setSerializeNull(true)
            );

        $response = new Respon($response, 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/update_avatar")
     * @View()
     */
    public function updateAvatarAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getManager();
        $data = json_decode($this->get("request")->getContent());
        $im = new ImageServices($this->container);
        $img = $im->base64_to_jpeg($data->img);

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findOneById($id);

        if($img){
            $fs = new Filesystem();
            if(file_exists($user->getAvatar())){
                $fs->remove(array($user->getAvatar()));
            }
        }

        $user->setAvatar($img);

        $em->flush();

        $posts = $user->getWall()->getPosts()->slice(0, 5);

        $user = array('user' => $user, 'posts' => $posts);

        $serializer = $this->container->get('jms_serializer');
        $response = $serializer
            ->serialize(
                $user,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
                    ->setGroups(array('getUser'))
            );


        $persister = $this->get('fos_elastica.object_persister.app.images');

        $images = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')
            ->createQueryBuilder('e')
            ->leftJoin('e.album', 'album')
            ->leftJoin('album.user', 'user')
            ->where('user.id = :items')
            ->setParameter('items', $id)
            ->getQuery()
            ->getResult();

        if($images){
            $persister->replaceMany($images);
        }

        $response = new Respon($response);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @return array
     * @Post("/v1/like")
     * @View()
     */
    public function likeAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getManager();
        $image_id = $this->get('request')->request->get('image_id');
        $redis = $this->get('snc_redis.default');
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $image = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')->findOneById($image_id);

        $album = $image->getAlbum();
        $user = $album->getUser();

        if($redis->sismember($image_id, $id)){
            $redis->srem($image_id, $id);

            $likes_album = $album->getLikes() - 1;
            $album->setLikes($likes_album);

            $likes_user = $user->getLikes() - 1;
            $user->setLikes($likes_user);
        }else{
            $redis->sadd($image_id, $id);

            $likes_album = $album->getLikes() + 1;
            $album->setLikes($likes_album);

            $likes_user = $user->getLikes() + 1;
            $user->setLikes($likes_user);
        }

        $likes = count($redis->smembers($image_id));
        $image->setLikes($likes);
        $em->flush();

        if(!empty($id) and $redis->sismember($image_id, $id)){
            $liked = true;
        }else{
            $liked = false;
        }

        if($image){
            $persister = $this->get('fos_elastica.object_persister.app.images');
            $persister->replaceOne($image);
        }

        $response = new Respon(json_encode(array('likes' => $likes, 'likes_album' => $likes_album, 'likes_user' => $likes_user, 'liked' => $liked)), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/get_likes_by_album_id")
     * @View()
     */
    public function getLikesByAlbumIdAction()
    {
        $id_album = $this->get('request')->request->get('id_album');
        $em = $this->getDoctrine()->getManager();
        $redis = $this->get('snc_redis.default');
        if($this->get('security.context')->isGranted('ROLE_USER')){
            $id = $this->get('security.context')->getToken()->getUser()->getId();
        }
        $album = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')->findOneById($id_album);

        $images = $album->getImages();
        $user = $album->getUser();

        $likes = array();

        foreach($images as $key=>$value){

           $id_img = $value->getId();

           if(!empty($id) and $redis->sismember($id_img, $id)){
               $likes[$id_img]['liked'] = true;
           }else{
               $likes[$id_img]['liked'] = false;
           }

        }

        $response = new Respon(json_encode(array('likes' => $likes)), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/image_previews")
     * @View()
     */
    public function imagePreviewsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $image_previews = $this->get('request')->request->get('image_previews');
        foreach($image_previews as $key => $value){
            $album = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')->find($key);

            if($album && $value){
                $count = count($image_previews[$key]);
                $viewsAlbum = $album->getViews() + $count;
                $album->setViews($viewsAlbum);
                $user = $album->getUser();
                $viewsUser = $user->getViews()+$count;
                $user->setViews($viewsUser);

                foreach($value as $v) {
                    $image = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')->find($v);
                    $viewsImage = $image->getViews() + 1;
                    $image->setViews($viewsImage);
                }
            }
        }
        $em->flush();
    }

    /**
     * @return array
     * @Post("/v1/edit_text_image")
     * @View()
     */
    public function editTextImageAction()
    {
        $id = $this->get('request')->request->get('id');
        $text = $this->get('request')->request->get('text');

        $em = $this->getDoctrine()->getManager();
        $image = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')->find($id);

        $image->setText($text);

        $em->flush();
    }

    /**
     * @return array
     * @Post("/v1/edit_description_album")
     * @View()
     */
    public function editDescriptionAlbumAction()
    {
        $id = $this->get('request')->request->get('id');
        $description = $this->get('request')->request->get('description');

        $em = $this->getDoctrine()->getManager();
        $album = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')->find($id);

        $album->setDescription($description);

        $em->flush();
    }

    /**
     * @return array
     * @Post("/v1/edit_name_album")
     * @View()
     */
    public function editNameAlbumAction()
    {
        $id = $this->get('request')->request->get('id');
        $name = $this->get('request')->request->get('name');

        $em = $this->getDoctrine()->getManager();
        $album = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')->find($id);

        $album->setName($name);

        $em->flush();
    }

    /**
     * @return array
     * @Post("/v1/edit_categories_album")
     * @View()
     */
    public function editCategoriesAlbumAction()
    {
        $id = $this->get('request')->request->get('id');
        $selectCategories = $this->get('request')->request->get('selectCategories');

        $em = $this->getDoctrine()->getManager();
        $album = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')->find($id);

        $oldCategories = $album->getCategories();

        if(!empty($album)){
            foreach($oldCategories as $cat){
                $album->removeCategory($cat);
            }
        }

        $categories = $em->getRepository("CreativerFrontBundle:Categories")->findBy(array('id' => $selectCategories));


        if(!empty($album)){
            foreach($categories as $cat){
                $album->addCategory($cat);
            }
            $em->flush($album);
        }

    }

    /**
 * @return array
 * @Post("/v1/edit_text_post")
 * @View()
 */
    public function editTextPostAction()
    {
        $id = $this->get('request')->request->get('id');
        $text = $this->get('request')->request->get('text');

        $em = $this->getDoctrine()->getManager();
        $post = $this->getDoctrine()->getRepository('CreativerFrontBundle:Posts')->find($id);

        $post->setText($text);

        $em->flush($post);

        $response = new Respon('', 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/send_data_post")
     * @View()
     */
    public function sendDataPostAction()
    {
        $id = $this->get('request')->request->get('id');
        $video = $this->get('request')->request->get('video');

        $em = $this->getDoctrine()->getManager();
        $post = $this->getDoctrine()->getRepository('CreativerFrontBundle:Posts')->find($id);

        foreach($video as $key=>$val){
            $post_video = new PostVideos();
            $post_video->setUrl($val);
            $post_video->setPost($post);
            $post->addPostVideo($post_video);
            $em->persist($post_video);
        }

        $em->flush();

        $serializer = $this->container->get('jms_serializer');
        $response = $serializer
            ->serialize(
                $post,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
                    ->setGroups(array('getUser'))
            );

        $response = new Respon($response);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @return array
     * @Post("/v1/remove_img_post")
     * @View()
     */
    public function removeImgPostAction()
    {
        $img_id = $this->get('request')->request->get('img_id');
        $post_id = $this->get('request')->request->get('post_id');


        $em = $this->getDoctrine()->getManager();
        $postImage = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostImages')->find($img_id);



        $path_img_post_thums = $this->container->getParameter('path_img_post_thums');
        $path_img_post_original = $this->container->getParameter('path_img_post_original');

        $path = $postImage->getPath();
        $name = $postImage->getName();

        if(file_exists($path_img_post_thums.$path.$name) && !empty($path) && !empty($name)){
            $fs = new Filesystem();
            $fs->remove(array($path_img_post_thums.$path.$name));
            $fs->remove(array($path_img_post_original.$path.$name));
            $em->remove($postImage);
        }

        $em->remove($postImage);
        $em->flush();

        $response = new Respon('', 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/remove_video_post")
     * @View()
     */
    public function removeVideoPostAction()
    {
        $video_id = (int)$this->get('request')->request->get('video_id');

        $em = $this->getDoctrine()->getManager();
        $video = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostVideos')->find($video_id);

        $em->remove($video);
        $em->flush();

        $response = new Respon('', 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/remove_document_post")
     * @View()
     */
    public function removeDocumentPostAction()
    {
        $document_id = $this->get('request')->request->get('document_id');
        $post_id = $this->get('request')->request->get('post_id');


        $em = $this->getDoctrine()->getManager();
        $postDocument = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostDocuments')->find($document_id);

        $path_documents = $this->container->getParameter('path_documents');

        $path = $postDocument->getPath();
        $name = $postDocument->getName();

        if(file_exists($path_documents.$path.$name) && !empty($path) && !empty($name)){
            $fs = new Filesystem();
            $fs->remove(array($path_documents.$path.$name));
            $em->remove($postDocument);
        }

        $em->remove($postDocument);
        $em->flush();

        $response = new Respon('', 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/search_people")
     * @View(serializerGroups={"searchPeople"})
     */
    public function searchPeopleAction()
    {
        $search_people = $this->get('request')->request->get('people_search');

        $users = $this->container->get('fos_elastica.finder.app.user');
        $keywordQuery = new \Elastica\Query\QueryString();

        if($search_people == 'undefined'){
            $keywordQuery->setQuery("id:"."*");
        }else{
            $keywordQuery->setQuery("username:".$search_people." OR lastname:".$search_people);
        }


        $people = $users->find($keywordQuery, '80');
        $people = array('people' => $people);

        return $people;
    }

    /**
     * @return array
     * @Post("/v1/feedback")
     * @View()
     */
    public function feedbackAction(){

        $nick = $this->get('request')->request->get('nick');
        $telephone = $this->get('request')->request->get('telephone');
        $email = $this->get('request')->request->get('email');
        $message = $this->get('request')->request->get('message');


        $mailer = $this->get('swiftmailer.mailer');
        $message = \Swift_Message::newInstance()
            ->setSubject('Обратная связь')
            ->setFrom($email)
            ->setTo('info@creativer.by')
            ->setBody($this->renderView('CreativerFrontBundle:Default:letter.html.twig', array('telephone' => $telephone, 'message' => $message, 'nick' => $nick, 'email' => $email)));
        $mailer->send($message);


        $response = new Respon('', 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    /**
     * @return array
     * @Post("/v1/remove_comment_album")
     * @View()
     */
    public function removeCommentAlbumAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $comment_id = $this->get('request')->request->get('id');
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $comment = $this->getDoctrine()->getRepository('CreativerFrontBundle:ImageComments')->find($comment_id);
        $image = $comment->getImage();


        $em->remove($comment);
        $em->flush();


        if($image){
            $persister = $this->get('fos_elastica.object_persister.app.images');
            $persister->replaceOne($image);
        }

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/set_viewed")
     * @View()
     */
    public function setViewedAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $id = $this->get('request')->request->get('id');
        $image = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')->find($id);

        $image->setViewed(true);

        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Post("/v1/change_tariff")
     * @View(serializerGroups={"getUser"})
     */
    public function changeTariffAction()
    {
        if(!$this->get('request')->request->get('id'))
        {
            $id = $this->get('security.context')->getToken()->getUser()->getId();
        }else{
            $id = $this->get('request')->request->get('id');
        }
        $tariff = $this->getDoctrine()->getRepository('CreativerFrontBundle:Tariffs')->find($id);
        $this->get('security.context')->getToken()->getUser()->setTariff($tariff);

        $user = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getEntityManager();
        $em->flush();

        $user = array('user' => $user);

        $serializer = $this->container->get('jms_serializer');
        $response = $serializer
            ->serialize(
                $user,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
                    ->setGroups(array('getUser'))
            );

        $response = new Respon($response);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Post("/v1/get_person_post_by_id")
     * @View(serializerGroups={"getUser"})
     */
    public function getPersonPostByIdAction()
    {
        $id = $this->get('request')->request->get('id');
        $post = $this->getDoctrine()->getRepository('CreativerFrontBundle:Posts')->find($id);

        $post = array('post' => $post);

        $serializer = $this->container->get('jms_serializer');
        $response = $serializer
            ->serialize(
                $post,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
                    ->setGroups(array('getUser'))
            );

        $response = new Respon($response);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}