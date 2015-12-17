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

//            /* @var $start \DateTime */
//            $start = new \DateTime('2015-11-07T20:59:59.000Z');
//
//            /* @var $end \DateTime */
//            $end = new \DateTime('2015-11-15T20:59:59.000Z');
//
//            $google = $this->get('google.client');
//
//            $google->setCredentialsJson();
//
//            $events = $google->getCalendar()->events->listEvents('infocreativer@gmail.com', [
//                'timeMin' => $start->format('c'),
//                'timeMax' => $end->format('c'),
//            ]);
//
//            die(var_dump($events));

        $other_date = $this->get('request')->request->get('date');
        $id_cat = $this->get('request')->request->get('id_cat');
        $target_date = $this->get('request')->request->get('target_date');
        $city = $this->get('request')->request->get('city')?$this->get('request')->request->get('city'):null;

        if(!empty($city['id'])){
            $city = $city['id'];
        }else{
            $city = null;
        }

        if($target_date) {
            $target_date = $date = new \DateTime($target_date);
            $target_date = $target_date->setTime(10, 00, 00)->format('Y/m/d H:i:s');
        }elseif($other_date){
            $current_date = $date = new \DateTime($other_date);
        }else{
            $current_date = $date = new \DateTime();
        }

        $month = (int)$date->format('m');
        $year = (int)$date->format('Y');


//        $start_month = $date->modify('first day of this month')->setTime(00, 00, 00)->format('Y/m/d H:i:s');
//        $end_month = $date->modify('last day of this month')->setTime(23, 59, 59)->format('Y/m/d H:i:s');

        if(!empty($current_date)){
            $start_month = (int)$current_date->format('d');
        }else{
            $start_month = null;
        }

        if($start_month && $start_month > 15){
            $start_month = $current_date->setTime(00, 00, 00)->format('d');
            $plus_period = 15 - (31 - (int)$start_month);
            $cr = clone $current_date;
            $end_month = $cr->modify('last day of this month')->modify('+ '.$plus_period.' day')->setTime(23, 59, 59)->format('Y/m/d H:i:s');
            $start_month = $current_date->setTime(01, 00, 00)->format('Y/m/d H:i:s');
        }else{
            $current_date = $date = new \DateTime($other_date);
            $start_month = $current_date->setTime(01, 00, 00)->format('Y/m/d H:i:s');
            $end_month = $date->modify('last day of this month')->setTime(23, 59, 59)->format('Y/m/d H:i:s');
        }

        if($id_cat != null) {

            $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')
                ->createQueryBuilder('e')
                ->select('cat.id as id_cat','cat.name as name_cat','e.id','e.name','e.description','e.path','e.img','e.start_date','e.end_date','e.viewed as viewed','e.count_comment as count_comment')
                ->leftJoin('e.event_sections', 'cat')
                ->leftJoin('e.event_city', 'city')
                ->where('cat IN (:items)');
                if($target_date){
                    $query->andWhere('e.end_date >= :date AND e.start_date <= :date')
                    ->setParameter('date', $target_date);
                }else{
                    $query->andWhere('e.start_date <= :end_month AND e.end_date >= :start_month')
                    ->setParameter('start_month', $start_month)
                    ->setParameter('end_month', $end_month);
                }
                $query->groupBy('e.id')
                ->orderBy('e.id', 'DESC');
                if($city){
                    $query->andWhere('city.id = :city')
                        ->setParameter('city', $city);
                }
                $query->setParameter('items', $id_cat);
                $query = $query->getQuery()->getResult();

            $mass[0]['events'] = $query;

        }else{

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
                        ->select('cat.id as id_cat','cat.name as name_cat','e.id','e.name','e.description','e.path','e.img','e.start_date','e.end_date','e.viewed as viewed','e.count_comment as count_comment')
                        ->leftJoin('e.event_sections', 'cat')
                        ->leftJoin('e.event_city', 'city')
                        ->where('cat IN (:items)');
                        if($target_date){
                                  $query->andWhere('e.end_date >= :date AND e.start_date <= :date')
                                  ->setParameter('date', $target_date);
                          }else{
                              $query->andWhere('e.start_date <= :end_month AND e.end_date >= :start_month')
                                  ->setParameter('start_month', $start_month)
                                  ->setParameter('end_month', $end_month);
                          }
                        $query->groupBy('e.id')
                        ->orderBy('e.id', 'DESC');
                        if($city){
                            $query->andWhere('city.id = :city')
                                  ->setParameter('city', $city);
                        }
                    $query->setParameter('items', $event);
                    $query = $query->getQuery()->getResult();

                    $mass[$i]['events'] = $query;
                }
                $i++;
            }

        }

        $next_date = $date->modify('first day of next month')->format('Y/m/d');
        $previous_date = $date->modify('first day of -1 month')->format('Y/m/d');
        if(empty($current_date)){
            $current_date = $target_date;
        }

        $result = array('year' => $year, 'month' => $month, 'events' => $mass, 'current_date' => $current_date, 'next_date' => $next_date, 'previous_date' => $previous_date);

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

        $id_cat = $this->get('request')->request->get('id_cat');


        if($id_cat != null){

            $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')
                ->createQueryBuilder('e')
                ->select('cat.id as id_cat','cat.name as name_cat','e.id','e.name','e.description','e.path','e.img','e.start_date','e.end_date')
                ->leftJoin('e.event_sections', 'cat')
                ->where('cat IN (:items)')
                ->groupBy('e.id')
                ->orderBy('e.id', 'DESC')
                ->setParameter('items', $id_cat);
            $query = $query->getQuery()->getResult();
            $mass[0]['events'] = $query;


        }else{
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
                        ->select('cat.id as id_cat','cat.name as name_cat','e.id','e.name','e.description','e.path','e.img','e.start_date','e.end_date')
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
     * @return array
     * @Post("/v1/get_event")
     * @View(serializerGroups={"getEvent"})
     */
    public function getEventAction(){
        $id = $this->get('request')->request->get('id');

        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')
            ->createQueryBuilder('e')
            ->where('e.id = :id')
            ->setParameter('id', $id);
        $event = $query->getQuery()->getResult()[0];

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
     * @return array
     * @Post("/v1/reviewed_event")
     * @View(serializerGroups={"getEvent"})
     */
    public function reviewedEventAction(){
        $id = $this->get('request')->request->get('id');

        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')
            ->createQueryBuilder('e')
            ->where('e.id = :id')
            ->setParameter('id', $id);
        $event = $query->getQuery()->getResult()[0];

        $count = $event->getViewed()+1;
        $event->setViewed($count);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($event);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
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

        $start_date = $start_date->setTime(01, 00, 00);
        $end_date = $end_date->setTime(23, 59, 59);

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
     * @Post("/v1/search_events")
     * @View(serializerGroups={"elastica"})
     */
    public function searchEventsAction()
    {
        $search_people = $this->get('request')->request->get('text');

        $users = $this->container->get('fos_elastica.finder.app.events');
        $keywordQuery = new \Elastica\Query\QueryString();

        if($search_people == 'undefined'){
            $keywordQuery->setQuery("id:"."*");
        }else{
            $keywordQuery->setQuery("name:".$search_people." OR description:".$search_people);
        }

        $events = $users->find($keywordQuery);
        $events = array('events' => $events);

        return $events;
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


        $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')
            ->createQueryBuilder('e')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()[0];

        $users = $event->getUsersAttend();

        $users = array('users' => $users, 'attend' => $attend);

        $serializer = $this->container->get('jms_serializer');
        $posts = $serializer
            ->serialize(
                $users,
                'json',
                SerializationContext::create()
                    ->setGroups(array('eventAttend'))
            );

        $response = new Respon($posts);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @return array
     * @Post("/v1/save_event_comment")
     * @View(serializerGroups={"getEvent"})
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

        $count_comment = $event->getCountComment()+1;
        $event->setCountComment($count_comment);


        $comment = new EventComments();

        $comment->setText($data->text)
            ->setEvent($event)
            ->setUser($user);


        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->persist($event);
        $em->flush();

        return $comment;
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


        if (file_exists($path_img_event_original . $path . $image) && !empty($path) && !empty($image)) {
            unlink($path_img_event_original . $path . $image);
        }

        $em->remove($event);
        $em->flush();


        $response = new Respon(json_encode(array()), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return array
     * @Post("/v1/get_news_events")
     * @View(serializerGroups={"getEvent"})
     */
    public function getNewsEventsAction()
    {
        $id = $this->get('request')->request->get('id');
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();

        $date = new \DateTime();

        $query = $em->createQueryBuilder();
        $query->select('e')
            ->from('CreativerFrontBundle:Events', 'e')
            ->where('e.end_date >= :date')
            ->setParameter('date',$date);
        $events = $query->getQuery()->getResult();

        $count = count($events);

        if($count > 1){
            $rand_keys = array_rand($events, 2);
            $events = array($events[$rand_keys[0]], $events[$rand_keys[1]]);
            return $events;
        }else{
            return $events;
        }

    }

    /**
     * @return array
     * @Post("/v1/get_soon_events")
     * @View(serializerGroups={"getEvent"})
     */
    public function getSoonEventsAction()
    {

        $user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
        $date = new \DateTime();


        $query = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')
            ->createQueryBuilder('e')
            ->leftJoin('e.users_attend', 'ue')
            ->where('ue = :user')
            ->andWhere('e.end_date >= :date')
            ->setParameter('user',$user)
            ->setParameter('date',$date);
        $events = $query->getQuery()->getResult();


        return $events;
    }

//    /**
//     * @return array
//     * @Post("/v1/remove_event")
//     * @View()
//     */
//    public function removeEventAction()
//    {
//        if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
//            $array = array('success' => false);
//            $response = new Respon(json_encode($array), 401);
//            $response->headers->set('Content-Type', 'application/json');
//
//            return $response;
//        }
//
//        $em = $this->getDoctrine()->getEntityManager();
//        $id = $this->get('request')->request->get('id');
//
//        $path_img_event_original = $this->container->getParameter('path_img_shop');
//
//
//        $event = $this->getDoctrine()->getRepository('CreativerFrontBundle:Events')->find($id);
//        $image = $event->getImg();
//        $path = $event->getPath();
//
//
//        unlink($path_img_event_original.$path.$image);
//
//        $em->remove($event);
//        $em->flush();
//
//
//        $response = new Respon(json_encode(array()), 200);
//        $response->headers->set('Content-Type', 'application/json');
//
//        return $response;
//    }

    /**
     * @return array
     * @Post("/v1/remove_comment_event")
     * @View()
     */
    public function removeCommentEventAction()
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
        $comment = $this->getDoctrine()->getRepository('CreativerFrontBundle:EventComments')->find($comment_id);

        $count_comment = $comment->getEvent()->getCountComment()-1;

        $comment->getEvent()->setCountComment($count_comment);

        $em->remove($comment);
        $em->flush();

        $array = array('success' => true);
        $response = new Respon(json_encode($array), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}