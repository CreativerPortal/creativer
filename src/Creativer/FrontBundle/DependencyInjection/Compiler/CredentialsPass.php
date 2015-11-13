<?php

namespace Creativer\FrontBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CredentialsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $googleClient = $container->getDefinition('google.client'); //??? ??????? ??????? ?? ???????? ? ?????????? ??????

        if ($container->hasParameter('google_p12_path') && $container->hasParameter('google_p12_email')) {
            $path = $container->getParameter('google_p12_path');
            $email = $container->getParameter('google_p12_email');

            $googleClient->addMethodCall('setCredentialsP12', [$path, $email]);
        } elseif ($container->hasParameter('google_json_path')) {
            $googleClient->addMethodCall('setCredentialsJson', [$container->getParameter('google_json_path')]);
        }
    }
}
