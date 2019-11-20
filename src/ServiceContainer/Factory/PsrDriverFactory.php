<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\ServiceContainer\Factory;

use Behat\Mink\Driver\BrowserKitDriver;
use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class PsrDriverFactory implements DriverFactory
{
    /**
     * @inheritDoc
     */
    public function getDriverName(): string
    {
        return 'psr';
    }

    /**
     * @inheritDoc
     */
    public function supportsJavascript(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function configure(ArrayNodeDefinition $builder): void
    {
    }

    /**
     * @inheritDoc
     */
    public function buildDriver(array $config): Definition
    {
        return new Definition(
            BrowserKitDriver::class,
            [
                new Reference('acpr.behat.psr.client'),
                '%mink.base_url%'
            ]
        );
    }
}
