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

        //$serializer = $this->container->get('jms_serializer');
        //$user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findOneById($data->id);
        //$user = $serializer->serialize($user, 'json');

        return array('post' => $post);
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
        if($this->get('security.context')->isGranted('ROLE_USER')){
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
        }else{
            $user = null;
        }

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
            if($val){
                $fs->remove(array($path_img_album_thums.$path_img_album_thums.$val->getPath().$val->getName()));
                $fs->remove(array($path_img_album_original.$path_img_album_original.$val->getPath().$val->getName()));
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

        $fs = new Filesystem();
            if($images){
                $fs->remove(array($path_img_album_thums.$images->getPath().$images->getName()));
                $fs->remove(array($path_img_album_original.$images->getPath().$images->getName()));
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
        $post = $this->getDoctrine()->getRepository('CreativerFrontBundle:Posts')->find($post_id);


        $path_img_post_thums = $this->container->getParameter('path_img_post_thums');
        $path_img_post_original = $this->container->getParameter('path_img_post_original');

        $images = $post->getPostImages();
        $comments = $post->getComments();

        if(!empty($comments)){
            foreach($comments as $key=>$val){
                $em->remove($val);
            }
        }

        if(!empty($images)){
            $fs = new Filesystem();
            foreach($images as $key=>$val){
                $fs->remove(array($path_img_post_thums.$val->getName()));
                $fs->remove(array($path_img_post_original.$val->getName()));
                $em->remove($val);
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

        if(!empty($id) and $redis->sismember($image_id, $id)){
            $liked = true;
        }else{
            $liked = false;
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

        if($postImage){
            $fs = new Filesystem();
            $fs->remove(array($path_img_post_thums.$postImage->getPath().$postImage->getName()));
            $fs->remove(array($path_img_post_original.$postImage->getPath().$postImage->getName()));
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
     * @Post("/v1/search_people")
     * @View()
     */
    public function searchPeopleAction()
    {
        $search_people = $this->get('request')->request->get('people_search');

        $users = $this->container->get('fos_elastica.finder.app.user');


        $keywordQuery = new \Elastica\Query\QueryString();
        $keywordQuery->setQuery("username:".$search_people." OR lastname:".$search_people);

        $people = $users->find($keywordQuery);

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
}