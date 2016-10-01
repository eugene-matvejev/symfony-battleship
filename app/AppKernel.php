<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles() : array
    {
        $bundles = [
            /** FRAMEWORK */
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
//            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            /** 3rd PARTY */
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            /** APPLICATION BUNDLES */
            new EM\FoundationBundle\FoundationBundle(),
            new EM\GameBundle\GameBundle()
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

    public function getRootDir() : string
    {
        return __DIR__;
    }

    public function getCacheDir() : string
    {
        return __DIR__ . '/../var/cache/' . $this->getEnvironment();
    }

    public function getLogDir() : string
    {
        return __DIR__ . '/../var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}
