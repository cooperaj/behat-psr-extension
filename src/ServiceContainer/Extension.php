<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\ServiceContainer;

use Acpr\Behat\Psr\ServiceContainer\Factory\PsrDriverFactory;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Extension implements ExtensionInterface
{
    /**
     * @inheritDoc
     * @codeCoverageIgnore
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
        /** @var MinkExtension|null $minkExtension */
        $minkExtension = $extensionManager->getExtension('mink');

        if ($minkExtension !== null) {
            $minkExtension->registerDriverFactory(new PsrDriverFactory());
        }
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function configure(ArrayNodeDefinition $builder): void
    {
        /**
         * @psalm-suppress PossiblyNullReference
         * @psalm-suppress PossiblyUndefinedMethod
         * @psalm-suppress MixedMethodCall
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

        /**
         * @psalm-suppress MixedArgument
         */
        $container->setParameter('acpr.behat.psr.container.file', $config[ 'container' ]);

        /**
         * @psalm-suppress MixedArgument
         */
        $container->setParameter('acpr.behat.psr.application.file', $config[ 'application' ]);
    }
}