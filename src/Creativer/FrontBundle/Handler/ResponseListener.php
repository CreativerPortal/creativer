<?php
/**
 * Created by PhpStorm.
 * User: slaq
 * Date: 06.09.2015
 * Time: 22:41
 */
namespace Creativer\FrontBundle\Handler;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ResponseListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
       // $event->getResponse()->headers->set('X-Frame-Options', 'SAMEORIGIN');
    }
}