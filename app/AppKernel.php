<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new AppBundle\AppBundle(),
            new Creativer\FrontBundle\CreativerFrontBundle(),
            new Creativer\ApiBundle\CreativerApiBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Snc\RedisBundle\SncRedisBundle(),
            new SunCat\MobileDetectBundle\MobileDetectBundle(),
            new FOS\ElasticaBundle\FOSElasticaBundle(),
            new FM\ElfinderBundle\FMElfinderBundle(),
            new Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle(),
            new Creativer\BackgroundBundle\CreativerBackgroundBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'prod'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }

//    public function getCacheDir()
//    {
//        if ($this->environment == 'dev' || $this->environment == 'prod') {
//            return '/tmp/cache/' . $this->environment;
//        } else {
//            return parent::getCacheDir();
//        }
//    }
//
//    public function getLogDir()
//    {
//        if ($this->environment == 'dev' || $this->environment == 'prod') {
//            return '/tmp/logs/' . $this->environment;
//        } else {
//            return parent::getLogDir();
//        }
//    }
}
