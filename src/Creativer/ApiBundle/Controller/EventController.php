<?php

namespace Creativer\ApiBundle\Controller;

use Blameable\Fixture\Entity\Comment;
use Creativer\FrontBundle\Entity\EventComments;
use Creativer\FrontBundle\Entity\Events;
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
     * @Post("/v1/get_datapicker")
     * @View()
     */
    public function getDatapickerAction(){
        $other_date = $this->get('request')->request->get('date');

        if($other_date){
            $current_date = $date = new \DateTime($other_date);
        }else{
            $current_date = $date = new \DateTime();
        }

        $month = $date->format('m');
        $year = $date->format('Y');


        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')
            ->createQueryBuilder('e')
            ->select('e.id','e.name','e.img','e.start_date','e.end_date')
            ->where('YEAR(e.end_date) = :year OR YEAR(e.start_date) = :year')
            ->andWhere('MONTH(e.end_date) = :month OR MONTH(e.start_date) = :month')
            ->groupBy('e.id')
            ->orderBy('e.id', 'DESC')
            ->setParameter('month', $month)
            ->setParameter('year', $year);
        $events = $query->getQuery()->getResult();

        $result = array('year' => $year, 'month' => $month, 'events' => $events, 'current_date' => $current_date);

        $serializer = $this->container->get('jms_serializer');
        $categories = $serializer
            ->serialize(
                $result,
                'json',
                SerializationContext::create()
                    ->enableMaxDepthChecks()
            );

        $response = new Respon($categories);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

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
                    ->select('cat.id as id_cat','e.id','e.name','e.path','e.img','e.start_date','e.end_date')
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
     * @View(serializerGroups={"getEvent"})
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
                    ->setGroups(array('getEvent'))
            );

        $response = new Respon($categories);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @return array
     * @Post("/v1/get_city_snd_sections")
     * @View(serializerGroups={"getCityAndSections"})
     */
    public function getCityAndSectionsAction(){
        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:EventSections')
            ->createQueryBuilder('e')
            ->addSelect('children')
            ->leftJoin('e.children', 'children')
            ->where('e.id = :id')
            ->setParameter('id', 1000);
        $eventSection = $query->getQuery()->getResult();


        $em = $this->getDoctrine()->getManager();
        $query = $em->createQueryBuilder();
        $query->select('e')
            ->from('CreativerFrontBundle:EventCity', 'e');
        $city = $query->getQuery()->getResult();


        $array = array('section' => $eventSection, "city" => $city);


        $serializer = $this->container->get('jms_serializer');
        $cityAndSections = $serializer
            ->serialize(
                $array,
                'json',
                SerializationContext::create()
                    ->setGroups(array('getCityAndSections'))
            );


        $response = new Respon($cityAndSections);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

        return array('city' => $response);
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
                    ->setGroups(array('getEvent'))
            );

        $response = new Respon($categories);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @return array
     * @Post("/v1/event_attend")
     * @View(serializerGroups={"eventAttend"})
     */
    public function eventAttendAction()
    {
        $id = $this->get('request')->request->get('id');
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();


        $query = $em->createQueryBuilder();
        $query
            ->select('u.id')
            ->from('CreativerFrontBundle:Events', 'e')
            ->innerJoin('e.users_attend', 'u')
            ->where('u = :user')
            ->andWhere('e.id = :id')
            ->setParameter('id',$id)
            ->setParameter('user', $user);
        $results = $query->getQuery()->getResult();


        if(!empty($results[0])){
            $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')->findById($id)[0];
            $event->removeUsersAttend($user);
            $user->removeEventsAttend($event);

            $em->persist($event);
            $em->persist($user);
            $em->flush();

            $attend = false;
        }else{
            $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')->findById($id)[0];
            $event->addUsersAttend($user);
            $user->addEventsAttend($event);

            $em->persist($event);
            $em->persist($user);
            $em->flush();

            $attend = true;
        }

        $users = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')->findById($id)[0]->getUsersAttend();

        $query = $em->createQueryBuilder();
        $query->select('COUNT(e)')
              ->from('CreativerFrontBundle:Events', 'e')
              ->innerJoin('e.users_attend', 'u')
              ->where('e.id = :id')
              ->setParameter('id',$id);
        $count = $query->getQuery()->getResult();


        $response = new Respon(json_encode(array('attend' => $attend,'count' => $count[0][1])), 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @return array
     * @Post("/v1/save_event_comment")
     * @View()
     */
    public function saveEventCommentAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        $data = json_decode($this->get("request")->getContent());

        $user = $this->get('security.context')->getToken()->getUser();

        $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')->findOneById($data->event_id);


        $comment = new EventComments();

        $comment->setText($data->text)
            ->setEvent($event)
            ->setUser($user);


        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        $view = \FOS\RestBundle\View\View::create()
            ->setStatusCode(200)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @return array
     * @Post("/v1/save_edit_event")
     * @View()
     */
    public function saveEditEventAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        $user = $this->get('security.context')->getToken()->getUser();

        $id = $this->get('request')->request->get('id');
        $description = $this->get('request')->request->get('description');
        $name = $this->get('request')->request->get('name');
        $event_city_id = $this->get('request')->request->get('event_city_id');
        $event_sections_id = $this->get('request')->request->get('event_sections_id');
        $start_date = $this->get('request')->request->get('start_date');
        $end_date = $this->get('request')->request->get('end_date');

        $start_date = new \DateTime($start_date);
        $end_date = new \DateTime($end_date);

        $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')->find($id);
        $eventSection = $this->getDoctrine()->getRepository('CreativerFrontBundle:EventSections')->find($event_sections_id);
        $eventCity = $this->getDoctrine()->getRepository('CreativerFrontBundle:EventCity')->find($event_city_id);
        $event->setEventSections($eventSection);
        $event->setEventCity($eventCity);
        $event->setDescription($description);
        $event->setName($name);
        $event->setStartDate($start_date);
        $event->setEndDate($end_date);

        $em = $this->getDoctrine()->getManager();
        $em->persist($event);
        $em->flush();

        $view = \FOS\RestBundle\View\View::create()
            ->setStatusCode(200)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }


    /**
     * @return array
     * @Post("/v1/delete_event")
     * @View()
     */
    public function deleteEventAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $array = array('success' => false);
            $response = new Respon(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $id = $this->get('request')->request->get('id');

        $path_img_event_original = $this->container->getParameter('path_img_event_original');


        $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')->find($id);
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

}