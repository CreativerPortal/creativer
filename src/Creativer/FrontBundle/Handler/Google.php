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

class Google
{
    /* @var \Google_Client */
    private $client;

    /* @var \Google_Service_Calendar */
    private $calendar;

    private $scope;

    public function __construct($scope)
    {
        $this->client = new \Google_Client();
        $this->scope = $scope;
    }

    public function setCredentialsJson()
    {
        $this->client->loadServiceAccountJson('/var/www/creativer/web/creativer-3af1c997e7f8.json', $this->scope);
    }

    /**
     * @return \Google_Service_Calendar
     */
    public function getCalendar()
    {
        if (!$this->calendar) {
            $this->calendar = new \Google_Service_Calendar($this->client);
        }

        return $this->calendar;
    }
}