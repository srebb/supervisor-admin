<?php

namespace Srebb\Bundle\SupervisorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SrebbSupervisorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $container->setParameter('srebb_supervisor.server_list', $config['server_list']);
        $container->setParameter('srebb_supervisor.update_interval', $config['update_interval']);

        $this->addAnnotatedClassesToCompile([
            'Srebb\\Bundle\\SupervisorBundle\\Server\\Server',
            'Srebb\\Bundle\\SupervisorBundle\\Server\\ServerFactory',
            'Srebb\\Bundle\\SupervisorBundle\\Controller\\IndexController',
            'Srebb\\Bundle\\SupervisorBundle\\Controller\\Api\\ServerController',
        ]);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        $rc = new \ReflectionClass(Configuration::class);

        $container->addResource(new FileResource($rc->getFileName()));

        return new Configuration($container->getParameter('kernel.debug'));
    }
}
