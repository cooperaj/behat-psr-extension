<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\Context\Initializer;

use Acpr\Behat\Psr\Context\Psr11AwareContext;
use Acpr\Behat\Psr\Context\Psr11MinkAwareContext;
use Acpr\Behat\Psr\RuntimeConfigurableKernel;
use Acpr\Behat\Psr\ServiceContainer\Factory\MinkSessionFactory;
use Acpr\Behat\Psr\ServiceContainer\Factory\PsrFactoryInterface;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer as BehatContextInitializer;
use Psr\Container\ContainerInterface;

class ContextInitializer implements BehatContextInitializer
{
    public function __construct(
        private PsrFactoryInterface $factory,
        private MinkSessionFactory $minkSessionFactory,
        private RuntimeConfigurableKernel $kernel,
    ) {}

    public function initializeContext(Context $context): void
    {
        $container = $this->factory->createContainer();
        $application = $this->factory->createApplication($container);

        if (!$container instanceof ContainerInterface) {
            throw new \RuntimeException(
                'It appears you are using your own Application/Container factory and have inappropriately ' .
                'altered or invalidated the Container as a part of your Application initialisation. In all cases a' .
                'PSR7 container must be available.'
            );
        }

        if ($context instanceof Psr11AwareContext || $context instanceof Psr11MinkAwareContext) {
            $context->setContainer($container);
        }

        if ($context instanceof Psr11MinkAwareContext) {
            $this->kernel->setApplication($application);
            $context->setMinkSession(($this->minkSessionFactory)($this->kernel));
        }
    }
}
