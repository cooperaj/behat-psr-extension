<?php

declare(strict_types=1);

namespace Acpr\Behat\Expressive\ServiceContainer\Factory;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ZendExpressiveFactory
{
    /**
     * @var string
     */
    private $containerFilePath;
    /**
     * @var string
     */
    private $applicationFilePath;

    public function __construct(string $containerFilePath, string $applicationFilePath)
    {
        $this->containerFilePath = $containerFilePath;
        $this->applicationFilePath = $applicationFilePath;
    }

    /**
     * Using the path to a file responsible for creating a PSR7 application
     * will return that application.
     *
     * Depending on your application a PSR11 container can be made available as $container
     *
     * @example features/bootstrap/app.php
     *
     * @param ContainerInterface|null $container
     * @return RequestHandlerInterface
     */
    public function createApplication(?ContainerInterface $container = null): RequestHandlerInterface
    {
        /** @psalm-suppress UnresolvableInclude */
        $application = require $this->applicationFilePath;

        if (!$application instanceof RequestHandlerInterface) {
            throw new \InvalidArgumentException('Loaded application is not a valid PSR7 application');
        }

        return $application;
    }

    /**
     * Using the path to a file responsible for creating a PSR11 container
     * will return that container.
     *
     * @example features/bootstrap/container.php
     *
     * @return ContainerInterface
     */
    public function createContainer(): ContainerInterface
    {
        /** @psalm-suppress UnresolvableInclude */
        $container = require $this->containerFilePath;

        if (!$container instanceof ContainerInterface) {
            throw new \InvalidArgumentException('Loaded container is not a valid PSR11 ContainerInterface');
        }

        return $container;
    }
}