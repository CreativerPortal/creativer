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
use Creativer\FrontBundle\Entity\Comments;
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
     * @View()
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


        $post->setUser($user)
            ->setText($data->text)
            ->setWall($wall);



        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        $serializer = $this->container->get('jms_serializer');
        $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findOneById($data->id);

        //$user = $serializer->serialize($user, 'json');

        return array('user' => $user);
    }

    /**
     * @return array
     * @Post("/v1/save_comment")
     * @View()
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

        $serializer = $this->container->get('jms_serializer');
        $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findOneById($data->id);

        //$user = $serializer->serialize($user, 'json');

        return array('user' => $user);
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

        $imageComment = new ImageComments();

        $imageComment->setUser($user)
            ->setImage($image)
            ->setText($data->text);

        $em = $this->getDoctrine()->getManager();
        $em->persist($imageComment);
        $em->flush();

        $image = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')->findOneById($data->image_id);

        return array('image_comments' => $image->getImageComments());
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
        $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findBy(array('id'=>$id));

        $user = array('user' => $user[0]);

        $serializer = $this->container->get('jms_serializer');
        $user = $serializer
            ->serialize(
                $user,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
            );


        $response = new Respon($user);
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

        return array('user' => $user);
    }



    /**
     * @Post("/v1/finish_upload")
     */
    public function finishUploadAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $data = json_decode($this->get("request")->getContent());
        $name = isset($data->name)?$data->name:'';
        $description = isset($data->description)?$data->description:'';
        $selectCategories = isset($data->selectCategories)?$data->selectCategories:'';

        $em = $this->getDoctrine();
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $em->getRepository("CreativerFrontBundle:User")->findBy(array('id' => $id));
        $album = $em->getRepository("CreativerFrontBundle:Albums")->findBy(array('user' => $user[0], 'isActive' => 0));

        $categories = $em->getRepository("CreativerFrontBundle:Categories")->findBy(array('id' => $selectCategories));


        if(!empty($album)){
            $album = $album[0];
            $album->setIsActive(1);
            $album->setName($name);
            $album->setDescription($description);
            foreach($categories as $cat){
                $album->addCategory($cat);
            }
            $em->getEntityManager()->flush($album);
        }


        $view = \FOS\RestBundle\View\View::create()
            ->setStatusCode(200)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @return array
     * @Post("/v1/get_user_by_album_id")
     * @View()
     */
    public function getUserByAlbumIdAction()
    {
        $id = $this->get('request')->request->get('id');
        $data = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')->find($id);
        $user = $data->getUser();

        return array('user' => $user);
    }

    /**
     * @return array
     * @Post("/v1/delete_image")
     * @View(serializerGroups={"idUserByIdImage"})
     */
    public function delete_imageAction()
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


        if($image->getName() && $id == $image->getAlbum()->getUser()->getId()){
            $fs = new Filesystem();
            $fs->remove(array($image->getName()));
            $em->remove($image);
        }

        $em->flush();


        return array('image' => $image);
    }

    /**
     * @return array
     * @Post("/v1/remove_post")
     * @View()
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
        $post = $this->getDoctrine()->getRepository('CreativerFrontBundle:Post')->find($post_id);



        $em->flush();


        return array('image' => $image);
    }

    /**
     * @return array
     * @Post("/v1/add_favorits")
     * @View()
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


        return array('user' => $newFriend);
    }



    /**
     * @return array
     * @Post("/v1/remove_favorits")
     * @View()
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

        return array('user' => $oldFriend);
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
            $fs->remove(array($user->getAvatar()));
        }

        $user->setAvatar($img);

        $em->flush();


        return array('user' => $user);
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

        $response = new Respon(json_encode(array('likes' => $likes, 'likes_album' => $likes_album, 'likes_user' => $likes_user)), 200);
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


}