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



class CatalogController extends Controller
{

    /**
     * @Post("/v1/get_categories")
     * @View()
     */
    public function getCategoriesAction()
    {
        $id = $this->get('request')->request->get('id');

        $category = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')->findBy(array('id'=>$id));

        $parent = $category[0]->getParent();

        while($parent->getParent()){
            $parent = $parent->getParent();
        }

        $parentId = $parent->getId();

        $categories = $this->getDoctrine()->getRepository('CreativerFrontBundle:Categories')->findBy(array('id'=>$parentId));

        $categories = array('category' => $category, 'categories' => $categories);

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