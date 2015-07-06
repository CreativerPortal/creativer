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
use Symfony\Component\HttpFoundation\Request;



class CatalogController extends Controller
{

    /**
     * @Post("/v1/get_services")
     * @View()
     */
    public function getServicesAction()
    {
        $id = $this->get('request')->request->get('id');
        $category = $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')->findBy(array('id'=>$id));
        $parent = $category[0]->getParent();

        while($parent->getParent()){
            $parent = $parent->getParent();
        }

        $parentId = $parent->getId();
        $categories = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')->findBy(array('id'=>$parentId));
        $categories = array('service' => $category, 'services' => $categories);

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
     * @Post("/v1/get_products")
     * @View()
     */
    public function getProductsAction(){
        $id = $this->get('request')->request->get('id');
        $category = $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')->findBy(array('id'=>$id));
        $parent = $category[0]->getParent();

        while($parent->getParent()){
            $parent = $parent->getParent();
        }

        $parentId = $parent->getId();
        $categories = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')->findBy(array('id'=>$parentId));
        $categories = array('product' => $category, 'products' => $categories);

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
     * @Post("/v1/get_catalog_product_albums")
     * @View()
     */
    public function getCatalogProductAlbumsAction(){

        $id = $this->get('request')->request->get('id');
        $page = $this->get('request')->request->get('page')?$this->get('request')->request->get('page'):1;
        $filter = $this->get('request')->request->get('filter')?$this->get('request')->request->get('filter'):'likes';

        $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')->findBy(array('id'=>$id));

        $i = 0;
        while(isset($items[$i])){
            if(!$items[$i]->getChildren()->isEmpty()){
                $childs = $items[$i]->getChildren();

                foreach($childs as $k => $v) {
                    array_push($items, $childs[$k]);
                }
            }
            $i++;
        }

        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')
            ->createQueryBuilder('e')
            ->join('e.album', 'alb')
            ->join('alb.categories', 'cat')
            ->where('cat IN (:items)')
            ->setParameter('items', $items);

            if($filter == 'likes'){
                $query->orderBy('e.likes', 'DESC');
            }elseif($filter == 'views'){
                $query->orderBy('e.views', 'DESC');
            }elseif($filter == 'date'){
                $query->orderBy('e.date', 'DESC');
            }

        $query = $query->getQuery();


        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            16
        );

        $products = array('currentPageNumber' => $pagination->getCurrentPageNumber(),
            'numItemsPerPage' => $pagination->getItemNumberPerPage(),
            'items' => $pagination->getItems(),
            'totalCount' => $pagination->getTotalItemCount());

        $products = array('products' => $products);

        //die(\Doctrine\Common\Util\Debug::dump($pagination));

        $serializer = $this->container->get('jms_serializer');
        $products = $serializer
            ->serialize(
                $products,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
            );

        $response = new Respon($products);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }


    /**
     * @Post("/v1/get_catalog_service_albums")
     * @View()
     */
    public function getCatalogServiceAlbumsAction(){

        $id = $this->get('request')->request->get('id');
        $page = $this->get('request')->request->get('page')?$this->get('request')->request->get('page'):1;
        $filter = $this->get('request')->request->get('filter')?$this->get('request')->request->get('filter'):'likes';


        $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')->findBy(array('id'=>$id));

        $i = 0;
        while(isset($items[$i])){
            if(!$items[$i]->getChildren()->isEmpty()){
                $childs = $items[$i]->getChildren();

                foreach($childs as $k => $v) {
                    array_push($items, $childs[$k]);
                }
            }
            $i++;
        }

        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')
            ->createQueryBuilder('e')
            ->join('e.categories', 'cat')
            ->where('cat IN (:items)')
            ->setParameter('items', $items);

            if($filter == 'likes'){
                $query->orderBy('e.likes', 'DESC');
            }elseif($filter == 'views'){
                $query->orderBy('e.views', 'DESC');
            }elseif($filter == 'date'){
                $query->orderBy('e.date', 'DESC');
            }

        $query = $query->getQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            8
        );

        $services = array('currentPageNumber' => $pagination->getCurrentPageNumber(),
            'numItemsPerPage' => $pagination->getItemNumberPerPage(),
            'items' => $pagination->getItems(),
            'totalCount' => $pagination->getTotalItemCount());

        $services = array('services' => $services);

        //die(\Doctrine\Common\Util\Debug::dump($pagination));

        $serializer = $this->container->get('jms_serializer');
        $services = $serializer
            ->serialize(
                $services,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
            );

        $response = new Respon($services);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }


    /**
     * @Post("/v1/get_post_by_id")
     * @View()
     */
    public function getPostByIdAction()
    {
        $post_id = $this->get('request')->request->get('post_id');

        $post = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->find($post_id);

        $post = array('post' => $post);

        $serializer = $this->container->get('jms_serializer');
        $categories = $serializer
            ->serialize(
                $post,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
            );

        $response = new Respon($categories);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Post("/v1/get_all_categories")
     * @View()
     */
    public function getAllCategoriesAction()
    {

        $categories = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')->findBy(array('parent' => null));

        $categories = array('categories' => $categories);

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


}