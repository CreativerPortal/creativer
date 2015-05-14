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
use Symfony\Component\BrowserKit\Response;


class PersonController extends Controller
{
    /**
     * @return array
     * @Get("/person_tmp")
     * @View()
     */
    public function personTmpAction()
    {

        return array();
    }

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
        $img = $this->get('security.context')->getToken()->getUser()->getImg();
        $userId = $this->get('security.context')->getToken()->getUser()->getId();

        $wall = $this->getDoctrine()->getRepository('CreativerFrontBundle:Wall')->findOneById($data->wall_id);


        $post = new Posts();


        $post->setUsername($username)
            ->setLastname($lastname)
            ->setImg($img)
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
        $img = $this->get('security.context')->getToken()->getUser()->getImg();
        $userId = $this->get('security.context')->getToken()->getUser()->getId();

        $post = $this->getDoctrine()->getRepository('CreativerFrontBundle:Posts')->findOneById($data->post_id);


        $comment = new Comments();

        $comment->setUsername($username)
            ->setLastname($lastname)
            ->setImg($img)
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
     * @Post("/v1/get_user")
     * @View()
     */
    public function getUserAction()
    {
        $serializer = $this->container->get('jms_serializer');
        if(!$this->get('request')->request->get('id'))
        {
            $id = $this->get('security.context')->getToken()->getUser()->getId();
        }else{
            $id = $this->get('request')->request->get('id');
        }
        $user = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')->findBy(array('id'=>$id));
        $json = $serializer->serialize($user, 'json');
        return array('user' => $user[0]);
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
        $name = $data->name;
        $description = $data->description;

        $em = $this->getDoctrine();
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $em->getRepository("CreativerFrontBundle:User")->findBy(array('id' => $id));
        $album = $em->getRepository("CreativerFrontBundle:Albums")->findBy(array('user' => $user[0], 'isActive' => 0));

        if(!empty($album)){
            $album = $album[0];
            $album->setIsActive(1);
            $album->setName($name);
            $album->setDescription($description);
            $em->getEntityManager()->flush($album);
        }


        $view = \FOS\RestBundle\View\View::create()
            ->setStatusCode(200)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}
