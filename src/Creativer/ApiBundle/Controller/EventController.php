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
     * @Post("/v1/get_events")
     * @View()
     */
    public function getEventsAction(){

        $items = $this->getDoctrine()->getRepository('CreativerFrontBundle:EventSections')->findBy(array('parent'=>1000));

        $i = 0;
        $mass = [];
        while(isset($items[$i])){
            $name = $items[$i]->getName();
            $mass[$i] = array('name'=>$name);
            if(!$items[$i]->getChildren()->isEmpty()){
                $childs = $items[$i]->getChildren();
                $event = [];
                foreach($childs as $k => $v) {
                    array_push($event, $childs[$k]);
                }

                $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')
                    ->createQueryBuilder('e')
                    ->select('cat.id as id_cat','e.id','e.name','e.img','e.start_date','e.end_date')
                    ->leftJoin('e.event_sections', 'cat')
                    ->where('cat IN (:items)')
                    ->groupBy('e.id')
                    ->orderBy('e.id', 'DESC')
                    ->setParameter('items', $event);
                $query = $query->getQuery()->getResult();

                $mass[$i]['events'] = $query;
            }
            $i++;
        }



        $serializer = $this->container->get('jms_serializer');
        $categories = $serializer
            ->serialize(
                $mass,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
            );

        $response = new Respon($categories);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Post("/v1/get_event")
     * @View()
     */
    public function getEventAction(){
        $id = $this->get('request')->request->get('id');
        $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')->find($id);

        $serializer = $this->container->get('jms_serializer');
        $categories = $serializer
            ->serialize(
                $event,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
            );

        $response = new Respon($categories);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Post("/v1/get_event_sections")
     * @View()
     */
    public function getEventSectionsAction(){
        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:EventSections')
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
        $eventSection = $query->getQuery()->getResult()[0];

        $categories = array('section' => $eventSection);

        $serializer = $this->container->get('jms_serializer');
        $categories = $serializer
            ->serialize(
                $categories,
                'json',
                SerializationContext::create()
                    ->setGroups(array('getEventSections'))
            );

        $response = new Respon($categories);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @return array
     * @Post("/v1/get_city")
     * @View(serializerGroups={"getCity"})
     */
    public function getCityAction()
    {
        $city = $this->getDoctrine()->getRepository('CreativerFrontBundle:EventCity')->findAll();

        return array('city' => $city);
    }

    /**
     * @return array
     * @Post("/v1/save_event")
     */
    public function saveEventAction()
    {
        $content = $this->get('request')->request->get('content');
        $city = $this->get('request')->request->get('city');
        $start_date = $this->get('request')->request->get('start_date');
        $end_date = $this->get('request')->request->get('end_date');
        $section = $this->get('request')->request->get('section');
        $title = $this->get('request')->request->get('title');

        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')->findBy(array('user'=>$user,'isActive'=>0))[0];

        $section = $this->getDoctrine()->getRepository('CreativerFrontBundle:EventSections')->findById($section)[0];
        $city = $this->getDoctrine()->getRepository('CreativerFrontBundle:EventCity')->findById($city)[0];

        $start_date = new \DateTime($start_date);
        $end_date = new \DateTime($end_date);

        $event->setEventSections($section);
        $event->setEventCity($city);
        $event->setDescription($content);
        $event->setStartDate($start_date);
        $event->setEndDate($end_date);
        $event->setName($title);
        $event->setIsActive(1);

        $em->persist($event);
        $em->flush();


        $serializer = $this->container->get('jms_serializer');
        $categories = $serializer
            ->serialize(
                $event,
                'json',
                SerializationContext::create()
                    ->setGroups(array('getPostById'))
            );

        $response = new Respon($categories);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}