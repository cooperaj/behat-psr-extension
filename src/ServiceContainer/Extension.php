<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\ServiceContainer;

use Acpr\Behat\Psr\ServiceContainer\Factory\PsrDriverFactory;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Exception;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @psalm-suppress UnusedClass
 */
final class Extension implements ExtensionInterface
{
    /**
     * @codeCoverageIgnore
     */
    #[\Override]
    public function process(ContainerBuilder $container): void {}

    #[\Override]
    public function getConfigKey(): string
    {
        return __NAMESPACE__;
    }

    #[\Override]
    public function initialize(ExtensionManager $extensionManager): void
    {
        /** @var MinkExtension|null $minkExtension */
        $minkExtension = $extensionManager->getExtension('mink');

        $minkExtension?->registerDriverFactory(new PsrDriverFactory());
    }

    /**
     * @codeCoverageIgnore
     */
    #[\Override]
    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->children()
                ->scalarNode('container')->defaultValue('config/container.php')->end()
                ->scalarNode('application')->defaultValue('config/app.php')->end()
            ->end()
        ->end();
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     * @return void
     * @throws Exception
     */
    #[\Override]
    public function load(ContainerBuilder $container, array $config): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('services.yml');

        $container->setParameter('acpr.behat.psr.container.file', (string)$config['container']);

        $container->setParameter('acpr.behat.psr.application.file', (string)$config['application']);
    }
}
