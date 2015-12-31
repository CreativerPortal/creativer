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
        //$categoriesBaraholka = $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:CategoriesBaraholka')->find(1000);

        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:CategoriesBaraholka')
            ->createQueryBuilder('e')
            ->addSelect('children')
            ->leftJoin('e.children', 'children')
            ->addSelect('twoChildren')
            ->leftJoin('children.children', 'twoChildren')
            ->addSelect('treeChildren')
            ->leftJoin('twoChildren.children', 'treeChildren')
            ->addSelect('fourChildren')
            ->leftJoin('treeChildren.children', 'fourChildren')
            ->where('e.id = :id')
            ->setParameter('id', 1000);
        $categoriesBaraholka = $query->getQuery()->getResult()[0];


        $postCity = $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostCity')->findAll();
        $postCategory = $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostCategory')->findAll();


        $categories = array('baraholka' => $categoriesBaraholka, 'post_city' => $postCity, 'post_category' => $postCategory);

        $serializer = $this->container->get('jms_serializer');
        $categories = $serializer
            ->serialize(
                $categories,
                'json',
                SerializationContext::create()
                    ->setGroups(array('getCategoriesBaraholka'))
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
        $post_category_id = $this->get('request')->request->get('post_category_id')?$this->get('request')->request->get('post_category_id'):0;
        $singboard_participate = $this->get('request')->request->get('singboard_participate');

        if($this->get('security.context')->isGranted('ROLE_USER')){
            $userId = $this->get('security.context')->getToken()->getUser()->getId();
        }else{
            $userId = null;
        }


        $page = $this->get('request')->request->get('page')?$this->get('request')->request->get('page'):1;


        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')
            ->createQueryBuilder('e')
            ->leftJoin('e.post_category', 'post_categ')
            ->addSelect('post_categ')
            ->leftJoin('e.images_baraholka', 'images');
            if($category_id != 'last'){
                $query->leftJoin('e.categories_baraholka', 'cat')
                ->where('cat IN (:items)')
                ->setParameter('items', $category_id);
            }


        if($city > 0 and $city != false){
            $query->join('e.post_city', 'city')
                ->andWhere('city = :city_id')
                ->setParameter('city_id', $city['id']);
        }
        if($post_category_id > 0){
            $query->andWhere('post_categ = :post_categ_id')
                ->setParameter('post_categ_id', $post_category_id);
        }
        if($new24 == true){
            $query->andWhere('e.date >= :dat')
                ->setParameter('dat', new \DateTime('-24 hours'));
        }
        if($my_singboard == true and $singboard_participate == true and $userId){
            $query->leftJoin('e.user', 'u')
                ->leftJoin('e.post_comments', 'pc')
                ->leftJoin('pc.user', 'userre')
                ->andWhere('u.id = :id or userre.id = :id')
                ->setParameter('id', $userId);
        }else if($my_singboard == true and $userId){
            $query->leftJoin('e.user', 'u')
                ->andWhere('u = :id')
                ->setParameter('id', $userId);
        }else if($singboard_participate == true and $userId){
            $query->leftJoin('e.post_comments', 'ps')
                ->leftJoin('ps.user', 'userr')
                ->andWhere('userr.id = :idd')
                ->setParameter('idd', $userId);
        }else{
            $query->leftJoin('e.user', 'u');
        }

        $query = $query->getQuery();


        $nameCategory = $this->getDoctrine()->getRepository('CreativerFrontBundle:CategoriesBaraholka')->find($category_id);


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

        if($category_id != 'last'){
            $posts['nameCategory'] = $nameCategory->getName();
        }else{
            $posts['nameCategory'] = 'Все объявления барахолки';
        }

        $posts = array('posts' => $posts);

        //die(\Doctrine\Common\Util\Debug::dump($pagination));

        $serializer = $this->container->get('jms_serializer');
        $posts = $serializer
            ->serialize(
                $posts,
                'json',
                SerializationContext::create()
                    ->setGroups(array('getPostsByCategory'))
            );

        $response = new Respon($posts);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }


    /**
     * @Post("/v1/get_post_by_id")
     * @View()
     */
    public function getPostByIdAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $post_id = $this->get('request')->request->get('post_id');

        if($post_id == 'undefined'){
            $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')
                ->createQueryBuilder('e')
                ->where('e.user = :user')
                ->setParameter('user', $user)
                ->setMaxResults(1)
                ->orderBy("e.id", 'DESC')
                ->getQuery()
                ->getResult()[0];
        }else{
            $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')
                ->createQueryBuilder('e')
                ->where('e.id = :id')
                ->setParameter('id', $post_id)
                ->getQuery()
                ->getResult()[0];
        }



        $post = array('post' => $query);

        $serializer = $this->container->get('jms_serializer');
        $categories = $serializer
            ->serialize(
                $post,
                'json',
                SerializationContext::create()
                    ->setGroups(array('getPostById'))
            );

        $response = new Respon($categories);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @return array
     * @Post("/v1/save_post_comment")
     * @View(serializerGroups={"getCommentBaraholka"})
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

        $user = $this->get('security.context')->getToken()->getUser();

        $post = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->findOneById($data->post_id);


        $comment = new PostComments();

        $comment->setText($data->text)
            ->setPostBaraholka($post)
            ->setUser($user);


        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();


        return $comment;
    }


    /**
     * @return array
     * @Post("/v1/id_check_post_category")
     * @View()
     */
    public function checkPostCategoryAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $id_check_post_category = $this->get('request')->request->get('id_check_post_category');

        $user = $this->get('security.context')->getToken()->getUser();

        $postBaraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->find($id);
        $postCategory = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostCategory')->find($id_check_post_category);
        $postBaraholka->setPostCategory($postCategory);



        $em = $this->getDoctrine()->getManager();
        $em->persist($postBaraholka);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/check_category_baraholka")
     * @View()
     */
    public function checkCategoryBaraholkaAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $id_check_category_baraholka = $this->get('request')->request->get('id_check_category_baraholka');

        $user = $this->get('security.context')->getToken()->getUser();

        $postBaraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->find($id);
        $categoriesBaraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:CategoriesBaraholka')->find($id_check_category_baraholka);
        $postBaraholka->setCategoriesBaraholka($categoriesBaraholka);



        $em = $this->getDoctrine()->getManager();
        $em->persist($postBaraholka);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/edit_title")
     * @View()
     */
    public function editTitleAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $title = $this->get('request')->request->get('title');

        $user = $this->get('security.context')->getToken()->getUser();

        $postBaraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->find($id);
        $postBaraholka->setName($title);

        $em = $this->getDoctrine()->getManager();
        $em->persist($postBaraholka);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/edit_city")
     * @View()
     */
    public function editCityAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $city = $this->get('request')->request->get('city');

        $user = $this->get('security.context')->getToken()->getUser();

        $postBaraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->find($id);
        $postCity = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostCity')->find($city);
        $postBaraholka->setPostCity($postCity);


        $em = $this->getDoctrine()->getManager();
        $em->persist($postBaraholka);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/edit_full_description")
     * @View()
     */
    public function editFullDescriptionAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $full_description = $this->get('request')->request->get('full_description');

        $user = $this->get('security.context')->getToken()->getUser();

        $postBaraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->find($id);
        $postBaraholka->setFullDescription($full_description);

        $em = $this->getDoctrine()->getManager();
        $em->persist($postBaraholka);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/edit_description")
     * @View()
     */
    public function editDescriptionAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $description = $this->get('request')->request->get('description');

        $user = $this->get('security.context')->getToken()->getUser();

        $postBaraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->find($id);
        $postBaraholka->setDescription($description);

        $em = $this->getDoctrine()->getManager();
        $em->persist($postBaraholka);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/edit_price")
     * @View()
     */
    public function editPriceAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $price = $this->get('request')->request->get('price');

        $user = $this->get('security.context')->getToken()->getUser();

        $postBaraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->find($id);
        $postBaraholka->setPrice($price);

        $em = $this->getDoctrine()->getManager();
        $em->persist($postBaraholka);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/edit_auction")
     * @View()
     */
    public function editAuctionAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $auction = $this->get('request')->request->get('auction');

        if($auction == 1){
            $auction = 0;
        }else{
            $auction = 1;
        }

        $user = $this->get('security.context')->getToken()->getUser();

        $postBaraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->find($id);
        $postBaraholka->setAuction($auction);

        $em = $this->getDoctrine()->getManager();
        $em->persist($postBaraholka);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/remove_image_baraholka")
     * @View()
     */
    public function removeImageBaraholkaAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $id_image = $this->get('request')->request->get('id_image');
        $user = $this->get('security.context')->getToken()->getUser();

        $postBaraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->find($id);
        $image = $this->getDoctrine()->getRepository('CreativerFrontBundle:ImagesBaraholka')->find($id_image);


        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->persist($postBaraholka);
        $em->flush();

        $path_img_baraholka_thums = $this->container->getParameter('path_img_baraholka_thums');
        $path_img_baraholka_original = $this->container->getParameter('path_img_baraholka_original');

        $fs = new Filesystem();

        $path = $image->getPath();
        $name = $image->getName();

        if (file_exists($path_img_baraholka_thums . $path . $name) && !empty($path) && !empty($name)) {
            $fs->remove(array($path_img_baraholka_thums . $path . $name));
        }
        if (file_exists($path_img_baraholka_thums . $path . $name) && !empty($path) && !empty($name)) {
            $fs->remove(array($path_img_baraholka_thums . $path . $path_img_baraholka_original . $name));
        }


        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/delete_post_baraholka")
     * @View()
     */
    public function deletePostBaraholkaAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $user = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        $postBaraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->find($id);
        $images = $postBaraholka->getImagesBaraholka();

        $path_img_baraholka_thums = $this->container->getParameter('path_img_baraholka_thums');
        $path_img_baraholka_original = $this->container->getParameter('path_img_baraholka_original');

        $fs = new Filesystem();

        foreach($images as $key=>$image){
            $path = $image->getPath();
            $name = $image->getName();

            if (file_exists($path_img_baraholka_thums . $path . $name) && !empty($path) && !empty($name)) {
                $fs->remove(array($path_img_baraholka_thums . $path . $name));
            }
            if (file_exists($path_img_baraholka_thums . $path . $name) && !empty($path) && !empty($name)) {
                $fs->remove(array($path_img_baraholka_thums . $path . $path_img_baraholka_original . $name));
            }

            $em->remove($image);
        }

        $em->remove($postBaraholka);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/main_image_baraholka")
     * @View()
     */
    public function mainImageBaraholkaAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $id_image = $this->get('request')->request->get('id_image');
        $user = $this->get('security.context')->getToken()->getUser();

        $postBaraholka = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostBaraholka')->find($id);
        $image = $this->getDoctrine()->getRepository('CreativerFrontBundle:ImagesBaraholka')->find($id_image);

        $postBaraholka->setImg($image->getName());

        $em = $this->getDoctrine()->getManager();
        $em->persist($postBaraholka);
        $em->flush();


        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/search_posts_baraholka_by_text")
     * @View()
     */
    public function searchPostsBaraholkaByTextAction()
    {
        $category_id = $this->get('request')->request->get('category_id');
        $search_text = $this->get('request')->request->get('search_text');
        $searchInCategory = $this->get('request')->request->get('searchInCategory');

        if(empty($search_text)){
            $search_text = '*';
        }

        if(empty($category_id)){
            $category_id = 1000;
        }

        $post_baraholka = $this->container->get('fos_elastica.finder.app.post_baraholka');
        $boolQuery = new \Elastica\Query\Bool();

        $fieldQuery = new \Elastica\Query\Match();
        $fieldQuery->setFieldQuery('full_description', $search_text);
        $boolQuery->addShould($fieldQuery);

        $fieldQuery = new \Elastica\Query\Match();
        $fieldQuery->setFieldQuery('description', $search_text);
        $boolQuery->addShould($fieldQuery);

        $fieldQuery = new \Elastica\Query\Match();
        $fieldQuery->setFieldQuery('name', $search_text);
        $boolQuery->addShould($fieldQuery);



        if($category_id != 1000){
            $categoryQuery = new \Elastica\Filter\Terms();
            $categoryQuery->setTerms('categories_baraholka.id', array($category_id));

            $filteredQuery = new \Elastica\Query\Filtered($boolQuery, $categoryQuery);
            $results = $post_baraholka->find($filteredQuery);
        }else{
            $keywordQuery = new \Elastica\Query\QueryString();
            $keywordQuery->setQuery("full_description:".$search_text." OR description:".$search_text." OR name:".$search_text);
            $results = $post_baraholka->find($keywordQuery, '80');
        }



        $results = array('posts' => $results, 'searchInCategory' => $searchInCategory);

        $serializer = $this->container->get('jms_serializer');
        $posts = $serializer
            ->serialize(
                $results,
                'json',
                SerializationContext::create()
                    ->setGroups(array('getPostsByCategory'))
            );


        $response = new Respon($posts);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @return array
     * @Post("/v1/remove_comment_baraholka")
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
        $comment = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostComments')->find($comment_id);

        $em->remove($comment);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}