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



class EventController extends Controller
{

    /**
     * @return array
     * @Post("/v1/get_city")
     * @View(serializerGroups={"getCity"})
     */
    public function getCityAction()
    {
        $city = $this->getDoctrine()->getRepository('CreativerFrontBundle:PostCity')->findAll();

        return array('city' => $city);
    }

}