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
use Creativer\FrontBundle\Entity\PostComments;
use Creativer\FrontBundle\Entity\ImageComments;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Filesystem\Filesystem;
use Creativer\FrontBundle\Services\ImageServices;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response as Respon;
use Symfony\Component\HttpFoundation\Request;



class BaraholkaController extends Controller
{
    /**
     * @Post("/v1/get_data_baraholka")
     * @View()
     */
    public function getCategoriesBaraholkaAction()
    {
        $categoriesBaraholka = $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:CategoriesBaraholka')->find(1000);
        $postCity = $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostCity')->findAll();
        $postCategory = $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostCategory')->findAll();


        $categories = array('baraholka' => $categoriesBaraholka, 'post_city' => $postCity, 'post_category' => $postCategory);

        $serializer = $this->container->get('jms_serializer');
        $categories = $serializer
            ->serialize(
                $categories,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
            );

        $response = new Respon($categories);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Post("/v1/get_posts_by_category")
     * @View()
     */
    public function getPostsByCategoryAction()
    {
        $category_id = $this->get('request')->request->get('category_id');
        $city = $this->get('request')->request->get('city');
        $my_singboard = $this->get('request')->request->get('my_singboard');
        $new24 = $this->get('request')->request->get('new24');
        $post_category_id = $this->get('request')->request->get('post_category_id');
        $singboard_participate = $this->get('request')->request->get('singboard_participate');



        $categoriesBaraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:CategoriesBaraholka')->find($category_id);
        $userId = $this->get('security.context')->getToken()->getUser()->getId();


        $page = $this->get('request')->request->get('page')?$this->get('request')->request->get('page'):1;


        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')
            ->createQueryBuilder('e')
            ->join('e.categories_baraholka', 'cat')
            ->where('cat IN (:items)')
            ->setParameter('items', $category_id);

        if($city > 0){
            $query->join('e.post_city', 'city')
                ->andWhere('city = :city_id')
                ->setParameter('city_id', $city);
        }
        if($post_category_id > 0){
            $query->join('e.post_category', 'type')
                ->andWhere('type = :type_id')
                ->setParameter('type_id', $post_category_id);
        }
        if($new24 == true){
            $query->andWhere('e.date >= :dat')
                ->setParameter('dat', new \DateTime('-24 hours'));
        }
        if($my_singboard == true and $singboard_participate == true and $userId){
            $query->leftJoin('e.user', 'u')
                ->leftJoin('e.post_comments', 'pc')
                ->andWhere('u.id = :id or pc.user_id = :id')
                ->setParameter('id', $userId);
        }else if($my_singboard == true and $userId){
            $query->join('e.user', 'u')
                ->andWhere('u = :id')
                ->setParameter('id', $userId);
        }else if($singboard_participate == true and $userId){
            $query->join('e.post_comments', 'ps')
                ->andWhere('ps.user_id = :idd')
                ->setParameter('idd', $userId);
        }

        $query = $query->getQuery();


        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            6
        );

        $posts = array('currentPageNumber' => $pagination->getCurrentPageNumber(),
            'numItemsPerPage' => $pagination->getItemNumberPerPage(),
            'items' => $pagination->getItems(),
            'totalCount' => $pagination->getTotalItemCount());

        $posts = array('posts' => $posts);

        //die(\Doctrine\Common\Util\Debug::dump($pagination));

        $serializer = $this->container->get('jms_serializer');
        $posts = $serializer
            ->serialize(
                $posts,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
            );

        $response = new Respon($posts);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }


    /**
     * @return array
     * @Post("/v1/save_post_comment")
     * @View()
     */
    public function savePostCommentAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        $data = json_decode($this->get("request")->getContent());

        $username = $this->get('security.context')->getToken()->getUser()->getUsername();
        $lastname = $this->get('security.context')->getToken()->getUser()->getLastname();
        $avatar = $this->get('security.context')->getToken()->getUser()->getAvatar();
        $userId = $this->get('security.context')->getToken()->getUser()->getId();

        $post = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->findOneById($data->post_id);


        $comment = new PostComments();

        $comment->setUsername($username)
            ->setLastname($lastname)
            ->setAvatar($avatar)
            ->setText($data->text)
            ->setPostBaraholka($post)
            ->setUserId($userId);


        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        $serializer = $this->container->get('jms_serializer');
        $post = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->findOneById($data->post_id);


        return array('post' => $post);
    }
}