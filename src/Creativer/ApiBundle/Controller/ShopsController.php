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



class ShopsController extends Controller
{
    /**
     * @Post("/v1/get_ctegories_shops")
     * @View(serializerGroups={"getShopsByCategory"})
     */
    public function getCtegoriesShopsAction()
    {

        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')
            ->createQueryBuilder('e')
            ->addSelect('parent')
            ->leftJoin('e.parent', 'parent')
            ->where('e.branch = :branch')
            ->setParameter('branch', 1);
        $catagoriesShops = $query->getQuery()->getResult();


        $categories = array('catagories_shops' => $catagoriesShops);


        return $categories;
    }

    /**
     * @Post("/v1/get_shops_by_category")
     * @View(serializerGroups={"getShopsByCategory"})
     */
    public function getShopsByCategoryAction()
    {
        $id = $this->get('request')->request->get('id');

        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Shops')
            ->createQueryBuilder('e')
            ->leftJoin('e.categories', 'cat')
            ->where('cat.id = :id')
            ->setParameter('id', $id);
        $shops = $query->getQuery()->getResult();

        $shops = array('shops' => $shops);

        return $shops;
    }

    /**
     * @Post("/v1/get_shop_by_id")
     * @View(serializerGroups={"getShopById"})
     */
    public function getShopByIdAction()
    {
        $id = $this->get('request')->request->get('id');

        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Shops')
            ->createQueryBuilder('e')
            ->where('e = :id')
            ->setParameter('id', $id);
        $shops = $query->getQuery()->getResult()[0];

        return $shops;
    }


    /**
     * @return array
     * @Post("/v1/remove_shop")
     * @View()
     */
    public function removeShopAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $id = $this->get('request')->request->get('id');

        $path_img_shop = $this->container->getParameter('path_img_shop');


        $shop = $this->getDoctrine()->getRepository('CreativerFrontBundle:Shops')->find($id);

        $category_id = $shop->getCategories()[0]->getId();

        $images = $shop->getImages();
        $fs = new Filesystem();

        foreach($images as $key=>$val){
            $image = $val->getName();
            $path = $val->getPath();
            if(file_exists($path_img_shop.$path.$image) && !empty($path) && !empty($image)){
                $fs->remove(array($path_img_shop.$path.$image));
            }
            $em->remove($val);
        }

        $image = $shop->getImg();
        $path = $shop->getPath();


        if(!empty($image)){
            $fs = new Filesystem();
            if(file_exists($path_img_shop.$path.$image) && !empty($path) && !empty($image)){
                $fs->remove(array($path_img_shop.$path.$image));
            }
        }


        $em->remove($shop);
        $em->flush();


        $response = new Respon(json_encode(array('id' => $category_id)), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/remove_image_shop")
     * @View()
     */
    public function removeImageShopAction()
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

        $shop = $this->getDoctrine()->getRepository('CreativerFrontBundle:Shops')->find($id);
        $image = $this->getDoctrine()->getRepository('CreativerFrontBundle:ImagesShops')->find($id_image);


        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->persist($shop);
        $em->flush();

        $path_img_shop = $this->container->getParameter('path_img_shop');

        $path = $image->getPath();
        $name = $image->getName();

        $fs = new Filesystem();
        if(file_exists($path_img_shop.$path.$name) && !empty($path) && !empty($name)){
            $fs->remove(array($path_img_shop.$path.$name));
        }

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/main_image_shop")
     * @View()
     */
    public function mainImageShopAction()
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

        $shops = $this->getDoctrine()->getRepository('CreativerFrontBundle:Shops')->find($id);
        $image = $this->getDoctrine()->getRepository('CreativerFrontBundle:ImagesShops')->find($id_image);

        $shops->setImg($image->getName());
        $shops->setPath($image->getPath());


        $em = $this->getDoctrine()->getManager();
        $em->persist($shops);
        $em->flush();


        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    /**
     * @return array
     * @Post("/v1/edit_name_shop")
     * @View()
     */
    public function editNameShopAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $title = $this->get('request')->request->get('name');

        $user = $this->get('security.context')->getToken()->getUser();

        $shop = $this->getDoctrine()->getRepository('CreativerFrontBundle:Shops')->find($id);
        $shop->setName($title);

        $em = $this->getDoctrine()->getManager();
        $em->persist($shop);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/edit_description_shop")
     * @View()
     */
    public function editDescriptionShopAction()
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

        $shop = $this->getDoctrine()->getRepository('CreativerFrontBundle:Shops')->find($id);
        $shop->setDescription($description);

        $em = $this->getDoctrine()->getManager();
        $em->persist($shop);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/edit_full_description_shop")
     * @View()
     */
    public function editFullDescriptionShopAction()
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

        $shop = $this->getDoctrine()->getRepository('CreativerFrontBundle:Shops')->find($id);
        $shop->setFullDescription($full_description);

        $em = $this->getDoctrine()->getManager();
        $em->persist($shop);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    /**
     * @return array
     * @Post("/v1/edit_site_shop")
     * @View()
     */
    public function editSiteShopAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $site = $this->get('request')->request->get('site');

        $user = $this->get('security.context')->getToken()->getUser();

        $shop = $this->getDoctrine()->getRepository('CreativerFrontBundle:Shops')->find($id);
        $shop->setSite($site);

        $em = $this->getDoctrine()->getManager();
        $em->persist($shop);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/edit_address_shop")
     * @View()
     */
    public function editAddressShopAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        //$id = $this->get('request')->request->get('id');
        $id_address = $this->get('request')->request->get('id_address');
        $address_text = $this->get('request')->request->get('address');


        $user = $this->get('security.context')->getToken()->getUser();

        $address = $this->getDoctrine()->getRepository('CreativerFrontBundle:Address')->find($id_address);
        $address->setAddress($address_text);

        $em = $this->getDoctrine()->getManager();
        $em->persist($address);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    /**
     * @return array
     * @Post("/v1/edit_telephone_shop")
     * @View()
     */
    public function editTelephoneShopAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $telephone = $this->get('request')->request->get('telephone');

        $user = $this->get('security.context')->getToken()->getUser();

        $shop = $this->getDoctrine()->getRepository('CreativerFrontBundle:Shops')->find($id);
        $shop->setTelephone($telephone);

        $em = $this->getDoctrine()->getManager();
        $em->persist($shop);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    /**
     * @return array
     * @Post("/v1/edit_email_shop")
     * @View()
     */
    public function editEmailShopAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $email = $this->get('request')->request->get('email');

        $user = $this->get('security.context')->getToken()->getUser();

        $shop = $this->getDoctrine()->getRepository('CreativerFrontBundle:Shops')->find($id);
        $shop->setEmail($email);

        $em = $this->getDoctrine()->getManager();
        $em->persist($shop);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/edit_working_time_shop")
     * @View()
     */
    public function editWorkingTimeShopAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $id = $this->get('request')->request->get('id');
        $working_time_shop = $this->get('request')->request->get('working_time');

        $user = $this->get('security.context')->getToken()->getUser();

        $shop = $this->getDoctrine()->getRepository('CreativerFrontBundle:Shops')->find($id);
        $shop->setWorkingTime($working_time_shop);

        $em = $this->getDoctrine()->getManager();
        $em->persist($shop);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}