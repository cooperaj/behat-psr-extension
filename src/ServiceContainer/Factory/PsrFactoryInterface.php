<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\ServiceContainer\Factory;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface PsrFactoryInterface
{
    /**
     * Using the path to a file responsible for creating a PSR7 application
     * will return that application.
     *
     * Depending on your application a PSR11 container can be made available as a preexisting $container
     * or created as a part of the application initialisation.
     *
     * @example features/bootstrap/app.php
     *
     * @param ContainerInterface|null $container
     * @return RequestHandlerInterface
     */
    public function createApplication(?ContainerInterface &$container = null): RequestHandlerInterface;

    /**
     * Using the path to a file responsible for creating a PSR11 container
     * will return that container.
     *
     * @example features/bootstrap/container.php
     *
     * @return ContainerInterface
     */
    public function createContainer(): ContainerInterface;
}
