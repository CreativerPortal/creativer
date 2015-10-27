<?php

namespace Creativer\BackgroundBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Creativer\BackgroundBundle\Document\Messages;


class DefaultController extends Controller
{
    /**
     * @Route("/background")
     * @Template()
     */
    public function backgroundAction()
    {
        $date = new \DateTime();
        $date1 = $date->format('Y-m-d');
        $date2 = $date->modify('-1 day')->format('Y-m-d');

        $date1 = new \DateTime($date1);
        $date2 = new \DateTime($date2);


        $messages = $this->get('doctrine_mongodb')
            ->getManager()
            ->createQueryBuilder('CreativerBackgroundBundle:Messages')
            ->field('reviewed')->equals(false)
            ->field('date')->lte($date1)
            ->field('date')->gte($date2)
            ->hydrate(false)
            ->getQuery()
            ->execute();

        $messages = iterator_to_array($messages);


        $mass = array();
        foreach($messages as $key=>$val){
            $mass[] = $val['receiver'];
        }

        $mass = array_unique($mass);

        $emails = $this->getDoctrine()->getRepository('CreativerFrontBundle:User')
            ->createQueryBuilder('e')
            ->select('e.email, e.username')
            ->where('e IN (:items)')
            ->andWhere('e.notification_message = :n_m')
            ->setParameter('items', $mass)
            ->setParameter('n_m', true)
            ->getQuery()
            ->getResult();


        foreach($emails as $key=>$val){
            $mailer = $this->get('swiftmailer.mailer.second_mailer');
            $message = \Swift_Message::newInstance()
                ->setSubject('Новое сообщение')
                ->setFrom(array('info@creativer.by' => 'Creativer'))
                ->setTo($val['email'])
                ->setContentType("text/html")
                ->setBody($this->renderView('CreativerFrontBundle:Default:letter_chat.html.twig', array('name' => $val['username'])));
            $mailer->send($message);
        }


        return array();
    }


    /**
     * @Route("/background_comment")
     * @Template()
     */
    public function backgroundCommentAction()
    {
        $date = new \DateTime();
        $date1 = $date->format('Y-m-d');
        $date2 = $date->modify('-1 day')->format('Y-m-d');

        $date1 = new \DateTime($date1);
        $date2 = new \DateTime($date2);


        $users = $this->getDoctrine()->getRepository('CreativerFrontBundle:Images')
            ->createQueryBuilder('e')
            ->select('user.id as id_user', 'user.email as email', 'user.username as username', 'user.lastname as lastname',  'album.id as id_album', 'album.name as name_album')
            ->leftJoin('e.image_comments', 'image_comments')
            ->leftJoin('e.album', 'album')
            ->leftJoin('album.user', 'user')
            ->where('e.viewed = :viewed')
            ->andWhere('user.notification_comment = :n_c')
            ->andWhere('image_comments.date <= :date1')
            ->andWhere('image_comments.date >= :date2')

            ->setParameter('viewed', false)
            ->setParameter('n_c', true)
            ->setParameter('date1', $date1)
            ->setParameter('date2', $date2)
            ->groupBy('user.id')
            ->getQuery()
            ->getResult();



        foreach($users as $key=>$val){
            $mailer = $this->get('swiftmailer.mailer.second_mailer');
            $message = \Swift_Message::newInstance()
                ->setSubject('Creativer')
                ->setFrom(array('info@creativer.by' => 'Creativer'))
                ->setTo($val['email'])
                ->setContentType("text/html")
                ->setBody($this->renderView('CreativerFrontBundle:Default:letter_comment.html.twig', array('username' => $val['username'],
                                                                                                           'lastname' => $val['lastname'],
                                                                                                           'id_album' => $val['id_album'],
                                                                                                           'name_album' => $val['name_album'])));
            $mailer->send($message);
        }


        return array();
    }

}
