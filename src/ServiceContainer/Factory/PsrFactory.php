<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\ServiceContainer\Factory;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PsrFactory implements PsrFactoryInterface
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
     * @inheritDoc
     */
    public function createApplication(?ContainerInterface &$container = null): RequestHandlerInterface
    {
        /**
         * @psalm-suppress UnresolvableInclude
         * @var RequestHandlerInterface $application
         */
        $application = require $this->applicationFilePath;

        if (!$application instanceof RequestHandlerInterface) {
            throw new \InvalidArgumentException('Loaded application is not a valid PSR7 application');
        }

        if (!$container instanceof ContainerInterface) {
            throw new \RuntimeException('A valid PSR11 ContainerInterface has not been created as a part of the application creation');
        }

        return $application;
    }

    /**
     * @inheritDoc
     */
    public function createContainer(): ContainerInterface
    {
        /**
         * @psalm-suppress UnresolvableInclude
         * @var ContainerInterface $container
         */
        $container = require $this->containerFilePath;

        if (!$container instanceof ContainerInterface) {
            throw new \InvalidArgumentException('Loaded container is not a valid PSR11 ContainerInterface');
        }

        return $container;
    }
}