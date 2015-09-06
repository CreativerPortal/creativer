<?php
/**
 * Created by PhpStorm.
 * User: slaq
 * Date: 06.09.2015
 * Time: 22:41
 */
namespace Creativer\FrontBundle\Handler;

use Doctrine\Common\EventArgs;
use FOS\ElasticaBundle\Doctrine\Listener as BaseListener;
use FOS\ElasticaBundle\Persister\ObjectPersister;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Creativer\FrontBundle\Entity\Albums;

class ElasticaCourseListener extends BaseListener
{
    private $container;
    private $objectPersisterSession;

    public function setContainer(ContainerInterface $container, ObjectPersister $objectPersisterSession)
    {
        $this->container = $container;
        $this->objectPersisterSession = $objectPersisterSession;
    }

    public function postUpdate(EventArgs $args)
    {
        $entity = $args->getEntity();
        die(var_dump($entity));
        if ($entity instanceof Albums) {
            $this->scheduledForUpdate[] = $entity;
            foreach ($entity->getSessions() as $session) {
                $this->objectPersisterSession->replaceOne($session);
            }
        }
    }
}