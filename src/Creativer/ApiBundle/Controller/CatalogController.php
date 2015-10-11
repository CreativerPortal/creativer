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
        $parentId = $this->container->getParameter('category_services');

        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')
            ->createQueryBuilder('e')
            ->addSelect('children')
            ->leftJoin('e.children', 'children')
            ->addSelect('twoChildren')
            ->leftJoin('children.children', 'twoChildren')
            ->addSelect('treeChildren')
            ->leftJoin('twoChildren.children', 'treeChildren')
            ->addSelect('fourChildren')
            ->leftJoin('treeChildren.children', 'fourChildren')
            ->where('e.id = :parent')
            ->setParameter('parent', $parentId);
        $categories = $query->getQuery()->getResult();

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
        $parentId = $this->container->getParameter('category_products');

        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')->searchCatalogProduct();

        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')
            ->createQueryBuilder('e')
            ->addSelect('children')
            ->leftJoin('e.children', 'children')
            ->addSelect('twoChildren')
            ->leftJoin('children.children', 'twoChildren')
            ->addSelect('treeChildren')
            ->leftJoin('twoChildren.children', 'treeChildren')
            ->addSelect('fourChildren')
            ->leftJoin('treeChildren.children', 'fourChildren')
            ->where('e.id = :parent')
            ->setParameter('parent', $parentId);
        $categories = $query->getQuery()->getResult();

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
     * @return array
     * @Post("/v1/get_catalog_product_albums")
     * @View(serializerGroups={"getCatalogProductAlbums"})
     */
    public function getCatalogProductAlbumsAction(){

        $id = $this->get('request')->request->get('id');
        $page = $this->get('request')->request->get('page')?$this->get('request')->request->get('page'):1;
        $filter = $this->get('request')->request->get('filter')?$this->get('request')->request->get('filter'):'likes';

        if(empty($id)){
            $id = 1000;
        }

        $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')->findBy(array('id'=>$id));

        $i = 0;
        while(isset($items[$i])){
            if(!$items[$i]->getChildren()->isEmpty()){
                $childs = $items[$i]->getChildren();
                foreach($childs as $k => $childs_two) {
                    if(!$childs_two->getChildren()->isEmpty()){
                        $childs_two = $items[$i]->getChildren();
                        foreach($childs_two as $kk => $childs_three) {
                            if(!$childs_three->getChildren()->isEmpty()) {
                                $childs_tree = $childs_three->getChildren();
                                foreach($childs_tree as $kkk => $childs_four) {
                                    if(!$childs_four->getChildren()->isEmpty()) {
                                        $childs_four = $childs_four->getChildren();
                                        foreach($childs_four as $kkkk => $childs_five) {
                                            array_push($items, $childs_five);
                                        }
                                    }else{
                                        array_push($items, $childs_four);
                                    }
                                }
                            }else{
                                array_push($items, $childs_three);
                            }
                        }
                    }else{
                        array_push($items, $childs_two);
                    }
                }
            }
            $i++;
        }

       //die(\Doctrine\Common\Util\Debug::dump($items));



        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')
            ->createQueryBuilder('e')
            ->addSelect('cat.id as id_cat', 'e.id','e.name','e.path','e.date','e.text as tex_img', 'e.likes as likes',
                     'alb.id as id_album','alb.name as name_album',
                     'usr.id as id_user','usr.username','usr.lastname')
            ->leftJoin('e.album', 'alb')
            ->leftJoin('alb.user', 'usr')
            ->leftJoin('alb.categories', 'cat')
            ->leftJoin('e.image_comments', 'comments')

            ->where('cat IN (:items)')
            ->groupBy('e.id')
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
            24
        );

        $products = array('currentPageNumber' => $pagination->getCurrentPageNumber(),
            'numItemsPerPage' => $pagination->getItemNumberPerPage(),
            'items' => $pagination->getItems(),
            'totalCount' => $pagination->getTotalItemCount());

        $products = array('products' => $products);

        //die(\Doctrine\Common\Util\Debug::dump($pagination));

//        $serializer = $this->container->get('jms_serializer');
//        $products = $serializer
//            ->serialize(
//                $products,
//                'json',
//                SerializationContext::create()
//                    ->enableMaxDepthChecks()
//            );
//
//        $response = new Respon($products);
//        $response->headers->set('Content-Type', 'application/json');
        return $products;
    }


    /**
     * @Post("/v1/get_catalog_service_albums")
     * @View(serializerGroups={"getCatalogServiceAlbums"})
     */
    public function getCatalogServiceAlbumsAction(){

        $id = $this->get('request')->request->get('id');
        $page = $this->get('request')->request->get('page')?$this->get('request')->request->get('page'):1;
        $filter = $this->get('request')->request->get('filter')?$this->get('request')->request->get('filter'):'likes';

        if(empty($id)){
            $id = 1001;
        }

        $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')->findBy(array('id'=>$id));

        $i = 0;
        while(isset($items[$i])){
            if(!$items[$i]->getChildren()->isEmpty()){
                $childs = $items[$i]->getChildren();
                foreach($childs as $k => $childs_two) {
                    if(!$childs_two->getChildren()->isEmpty()){
                        $childs_two = $items[$i]->getChildren();
                        foreach($childs_two as $kk => $childs_three) {
                            if(!$childs_three->getChildren()->isEmpty()) {
                                $childs_tree = $childs_three->getChildren();
                                foreach($childs_tree as $kkk => $childs_four) {
                                    if(!$childs_four->getChildren()->isEmpty()) {
                                        $childs_four = $childs_four->getChildren();
                                        foreach($childs_four as $kkkk => $childs_five) {
                                            array_push($items, $childs_five);
                                        }
                                    }else{
                                        array_push($items, $childs_four);
                                    }
                                }
                            }else{
                                array_push($items, $childs_three);
                            }
                        }
                    }else{
                        array_push($items, $childs_two);
                    }
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
            12
        );

        $services = array('currentPageNumber' => $pagination->getCurrentPageNumber(),
            'numItemsPerPage' => $pagination->getItemNumberPerPage(),
            'items' => $pagination->getItems(),
            'totalCount' => $pagination->getTotalItemCount());

        $services = array('services' => $services);

        //die(\Doctrine\Common\Util\Debug::dump($pagination));

//        $serializer = $this->container->get('jms_serializer');
//        $services = $serializer
//            ->serialize(
//                $services,
//                'json',
//                SerializationContext::create()
//                    ->enableMaxDepthChecks()
//            );
//
//        $response = new Respon($services);
//        $response->headers->set('Content-Type', 'application/json');
        return $services;

    }


    /**
     * @Post("/v1/get_all_categories")
     * @View()
     */
    public function getAllCategoriesAction()
    {

        //$categories = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')->findBy(array('parent' => null));


        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')
            ->createQueryBuilder('e')
            ->addSelect('children')
            ->leftJoin('e.children', 'children')
            ->addSelect('twoChildren')
            ->leftJoin('children.children', 'twoChildren')
            ->addSelect('treeChildren')
            ->leftJoin('twoChildren.children', 'treeChildren')
            ->addSelect('fourChildren')
            ->leftJoin('treeChildren.children', 'fourChildren')
            ->where('e.parent IS NULL');
        $categories = $query->getQuery()->getResult();


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

    /**
     * @Post("/v1/get_all_categories_with_album_categories")
     * @View()
     */
    public function getAllCategoriesWithAlbumCategoriesAction()
    {
        $post_id = $this->get('request')->request->get('post_id');


//        $ids = $this->getDoctrine()->getRepository('CreativerFrontBundle:Albums')
//            ->createQueryBuilder('e')
//            ->select("cat.id, 'true' as selected")
//            ->leftJoin('e.categories', 'cat')
//            ->where('e.id = 1')
//            ->getQuery()
//            ->getResult();
//        die(var_dump($ids));


//        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')
//            ->createQueryBuilder('e')
//            ->leftJoin("(select categories.*, 'true' as selected from albums
//                        left join albums_categories on albums.id = albums_categories.albums_id
//                        left join categories on categories.id = albums_categories.categories_id
//                        where albums.id = 1)", 'cat_true on categories.id = cat_true.id')
//            ->getQuery()
//            ->getResult();


        //die(var_dump($query));
        //die(\Doctrine\Common\Util\Debug::dump($query));


        $ids = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')
            ->createQueryBuilder('e')
            ->getQuery()
            ->getScalarResult();

        die(var_dump($ids));


        $serializer = $this->container->get('jms_serializer');
        $categories = $serializer
            ->serialize(
                $query,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
            );

        $response = new Respon($categories);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @return array
     * @Post("/v1/search_products")
     * @View()
     */
    public function searchProductsAction()
    {
        $search_text = $this->get('request')->request->get('search_text');

        $products = $this->container->get('fos_elastica.finder.app.images');
        $boolQuery = new \Elastica\Query\Bool();

        $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')->findBy(array('id'=>1000));
        $items_id = [];

        $i = 0;
        while(isset($items[$i])){
            if(!$items[$i]->getChildren()->isEmpty()){
                $childs = $items[$i]->getChildren();

                foreach($childs as $k => $v) {
                    array_push($items, $childs[$k]);
                    array_push($items_id, $childs[$k]->getId());
                }
            }
            $i++;
        }


        $keywordQuery = new \Elastica\Query\QueryString();
        $keywordQuery->setQuery("name_album:".$search_text." OR text:".$search_text." OR album.description:".$search_text);
        $boolQuery->addShould($keywordQuery);


        $optionKeyTerm = new \Elastica\Filter\Terms();
        $optionKeyTerm->setTerms('album.categories.id',  array($items_id));
        $nested = new \Elastica\Filter\Nested();
        $nested->setFilter($optionKeyTerm);
        $nested->setPath('album.categories');


        $filteredQuery = new \Elastica\Query\Filtered($boolQuery, $nested);
        $posts = $products->findHybrid($filteredQuery);


        foreach ($posts as $hybridResult) {

            $results[] = $hybridResult->getResult()->getHit()["_source"];
        }
        $products = array('products' => array('items' => $results));

        return $products;
    }

    /**
     * @return array
     * @Post("/v1/search_services")
     * @View()
     */
    public function searchServicesAction()
    {
        $search_text = $this->get('request')->request->get('search_text');

        $products = $this->container->get('fos_elastica.finder.app.album');
        $boolQuery = new \Elastica\Query\Bool();

        $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')->findBy(array('id'=>1001));
        $items_id = [];

        $i = 0;
        while(isset($items[$i])){
            if(!$items[$i]->getChildren()->isEmpty()){
                $childs = $items[$i]->getChildren();

                foreach($childs as $k => $v) {
                    array_push($items, $childs[$k]);
                    array_push($items_id, $childs[$k]->getId());
                }
            }
            $i++;
        }


        $keywordQuery = new \Elastica\Query\QueryString();
        $keywordQuery->setQuery("name:".$search_text." OR description:".$search_text." OR images.text:".$search_text);
        $boolQuery->addShould($keywordQuery);



        $optionKeyTerm = new \Elastica\Filter\Terms();
        $optionKeyTerm->setTerms('categories.id',  array($items_id));
        $nested = new \Elastica\Filter\Nested();
        $nested->setFilter($optionKeyTerm);
        $nested->setPath('categories');


        $filteredQuery = new \Elastica\Query\Filtered($boolQuery, $nested);
        $posts = $products->findHybrid($filteredQuery);


        foreach ($posts as $hybridResult) {

            $results[] = $hybridResult->getResult()->getHit()["_source"];
        }
        $products = array('products' => array('items' => $results));

        return $products;
    }

    /**
     * @return array
     * @Post("/v1/get_likes_by_images_id")
     * @View()
     */
    public function getLikesByImagesIdAction()
    {
        $images_id = $this->get('request')->request->get('images_id');
        $em = $this->getDoctrine()->getManager();
        $redis = $this->get('snc_redis.default');
        if($this->get('security.context')->isGranted('ROLE_USER')){
            $id = $this->get('security.context')->getToken()->getUser()->getId();
        }


        $likes = array();

        foreach($images_id as $key=>$id_img){

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
}