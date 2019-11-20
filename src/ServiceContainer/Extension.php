<?php

declare(strict_types=1);

namespace Acpr\Behat\Expressive\ServiceContainer;

use Acpr\Behat\Expressive\ServiceContainer\Factory\ZendExpressiveFactory;
use Acpr\Behat\Expressive\ServiceContainer\Factory\PsrDriverFactory;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Zend\Expressive\Application;

class Extension implements ExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container): void {}

    /**
     * @inheritDoc
     */
    public function getConfigKey(): string
    {
        return __NAMESPACE__;
    }

    /**
     * @inheritDoc
     */
    public function initialize(ExtensionManager $extensionManager): void
    {
        /** @var MinkExtension $minkExtension */
        $minkExtension = $extensionManager->getExtension('mink');

        /** @psalm-suppress RedundantConditionGivenDocblockType */
        if ($minkExtension !== null) {
            $minkExtension->registerDriverFactory(new PsrDriverFactory());
        }
    }

    /**
     * @inheritDoc
     */
    public function configure(ArrayNodeDefinition $builder): void
    {
        /**
         * @psalm-suppress PossiblyNullReference
         * @psalm-suppress PossiblyUndefinedMethod
         */
        $builder
            ->children()
                ->scalarNode('container')->defaultValue('config/container.php')->end()
                ->scalarNode('application')->defaultValue('config/app.php')->end()
            ->end()
        ->end();
    }

    /**
     * @inheritDoc
     */
    public function load(ContainerBuilder $container, array $config): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('services.yml');

        $container->setParameter('acpr.behat.expressive.container.file', $config[ 'container' ]);
        $container->setParameter('acpr.behat.expressive.application.file', $config[ 'application' ]);

        $this->loadApplicationFactory($container);
        $this->loadExpressiveContainer($container);
        $this->loadExpressiveApplication($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    public function loadApplicationFactory(ContainerBuilder $container): void
    {
        $expressiveApplicationFactory = new Definition(ZendExpressiveFactory::class, [
            '%acpr.behat.expressive.container.file%',
            '%acpr.behat.expressive.application.file%'
        ]);
        $expressiveApplicationFactory->setShared(true);
        $container->setDefinition('acpr.behat.expressive.factory', $expressiveApplicationFactory);
    }

    /**
     * @param ContainerBuilder $container
     */
    public function loadExpressiveContainer(ContainerBuilder $container): void
    {
        $expressiveContainer = new Definition(ContainerInterface::class);
        $expressiveContainer->setFactory([new Reference('acpr.behat.expressive.factory'), 'createContainer']);
        $container->setDefinition('acpr.behat.expressive.container', $expressiveContainer);
    }

    /**
     * @param ContainerBuilder $container
     */
    public function loadExpressiveApplication(ContainerBuilder $container): void
    {
        $expressiveApp = new Definition(Application::class, [
            new Reference('acpr.behat.expressive.container')
        ]);
        $expressiveApp->setFactory([new Reference('acpr.behat.expressive.factory'), 'createApplication']);
        $container->setDefinition('acpr.behat.expressive.application', $expressiveApp);
    }
}