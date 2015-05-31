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



class PersonController extends Controller
{

    /**
     * @return array
     * @Post("/v1/save_post")
     * @View()
     */
    public function savePostAction()
    {
        $data = json_decode($this->get("request")->getContent());


        $username = $this->get('security.context')->getToken()->getUser()->getUsername();
        $lastname = $this->get('security.context')->getToken()->getUser()->getLastname();
        $avatar = $this->get('security.context')->getToken()->getUser()->getAvatar();
        $userId = $this->get('security.context')->getToken()->getUser()->getId();

        $wall = $this->getDoctrine()->getRepository('CreativerFrontBundle:Wall')->findOneById($data->wall_id);


        $post = new Posts();


        $post->setUsername($username)
            ->setLastname($lastname)
            ->setAvatar($avatar)
            ->setText($data->text)
            ->setWall($wall)
            ->setUserId($userId);



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
        $data = json_decode($this->get("request")->getContent());


        $username = $this->get('security.context')->getToken()->getUser()->getUsername();
        $lastname = $this->get('security.context')->getToken()->getUser()->getLastname();
        $avatar = $this->get('security.context')->getToken()->getUser()->getAvatar();
        $userId = $this->get('security.context')->getToken()->getUser()->getId();

        $post = $this->getDoctrine()->getRepository('CreativerFrontBundle:Posts')->findOneById($data->post_id);


        $comment = new Comments();

        $comment->setUsername($username)
            ->setLastname($lastname)
            ->setAvatar($avatar)
            ->setText($data->text)
            ->setPost($post)
            ->setUserId($userId);


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
     * @Post("/v1/get_image_comments")
     * @View(serializerGroups={"getImageComments"})
     */
    public function getImageCommentsAction()
    {
        $data = json_decode($this->get("request")->getContent());

        $image = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')->findOneById($data->image_id);

        return array('image_comments' => $image->getImageComments());
    }

    /**
     * @return array
     * @Post("/v1/save_image_comments")
     * @View(serializerGroups={"getImageComments"})
     */
    public function saveImageCommentsAction()
    {
        $data = json_decode($this->get("request")->getContent());

        $avatar = $this->get('security.context')->getToken()->getUser()->getAvatar();

       // die(\Doctrine\Common\Util\Debug::dump($avatar));

        $image = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')->findOneById($data->image_id);

        $imageComment = new ImageComments();

        $imageComment->setAvatar($avatar)
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

        $data = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')->findBy(array('id'=>$id));

        $user = $data[0]->getUser();

        return array('user' => $user);
    }

    /**
     * @return array
     * @Post("/v1/delete_image")
     * @View(serializerGroups={"idUserByIdImage"})
     */
    public function delete_imageAction()
    {
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
     * @Post("/v1/add_favorits")
     * @View()
     */
    public function addFavoritsAction()
    {

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

        $em = $this->getDoctrine()->getManager();

        $data = json_decode($this->get("request")->getContent());


        $im = new ImageServices($this->container);
        $img = $im->base64_to_jpeg($data->img);


        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findOneById($id);

        if($img){
            $fs = new Filesystem();
            $fs->remove(array($user->getAvatar()->getImg()));
        }

        $user->getAvatar()->setImg($img);

        $em->flush();


        return array('user' => $user);
    }


}