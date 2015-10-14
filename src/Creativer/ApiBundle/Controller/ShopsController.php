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
     * @View(serializerGroups={"getAlbumById"})
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
     * @View(serializerGroups={"getAlbumById"})
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
     * @return array
     * @Post("/v1/remove_event")
     * @View()
     */
    public function removeEventAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $id = $this->get('request')->request->get('id');

        $path_img_event_original = $this->container->getParameter('path_img_shop');


        $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:Shops')->find($id);
        $image = $event->getImg();
        $path = $event->getPath();


        $fs = new Filesystem();
        $fs->remove(array($path_img_event_original.$path.$image));

        $em->remove($event);
        $em->flush();


        $response = new Respon(json_encode(array()), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
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
        $image = $shop->getImg();
        $path = $shop->getPath();


        $fs = new Filesystem();
        $fs->remove(array($path_img_shop.$path.$image));

        $em->remove($shop);
        $em->flush();


        $response = new Respon(json_encode(array()), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}