<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\ServiceContainer\Factory;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class PsrFactory implements PsrFactoryInterface
{
    public function __construct(
        readonly private string $containerFilePath,
        readonly private string $applicationFilePath,
    ) {}

    public function createApplication(?ContainerInterface &$container = null): RequestHandlerInterface
    {
        /**
         * @psalm-suppress UnresolvableInclude
         */
        $application = require $this->applicationFilePath;

        if (!$application instanceof RequestHandlerInterface) {
            throw new InvalidArgumentException('Loaded application is not a valid PSR7 application');
        }

        if (!$container instanceof ContainerInterface) {
            throw new RuntimeException(
                'A valid PSR11 ContainerInterface has not been created as a part of the application creation'
            );
        }

        return $application;
    }

    public function createContainer(): ContainerInterface
    {
        /**
         * @psalm-suppress UnresolvableInclude
         */
        $container = require $this->containerFilePath;

        if (!$container instanceof ContainerInterface) {
            throw new InvalidArgumentException('Loaded container is not a valid PSR11 ContainerInterface');
        }

        return $container;
    }
}
