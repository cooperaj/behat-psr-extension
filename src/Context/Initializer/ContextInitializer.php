<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\Context\Initializer;

use Acpr\Behat\Psr\Context\Psr11AwareContext;
use Acpr\Behat\Psr\Context\Psr11MinkAwareContext;
use Acpr\Behat\Psr\RuntimeConfigurableKernel;
use Acpr\Behat\Psr\ServiceContainer\Factory\MinkSessionFactory;
use Acpr\Behat\Psr\ServiceContainer\Factory\PsrFactory;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer as BehatContextInitializer;

class ContextInitializer implements BehatContextInitializer
{
    /**
     * @var PsrFactory
     */
    private $factory;
    /**
     * @var MinkSessionFactory
     */
    private $minkSessionFactory;
    /**
     * @var RuntimeConfigurableKernel
     */
    private $kernel;

    public function __construct(
        PsrFactory $factory,
        MinkSessionFactory $minkSessionFactory,
        RuntimeConfigurableKernel $kernel)
    {
        $this->factory = $factory;
        $this->minkSessionFactory = $minkSessionFactory;
        $this->kernel = $kernel;
    }

    public function initializeContext(Context $context): void
    {
        $container = $this->factory->createContainer();
        $application = $this->factory->createApplication($container);

        if ($container === null) {
            throw new \RuntimeException(
                'It appears you are using your own Application/Container factory and have not appropriately ' .
                'created either the ContainerInterface or RequestHandlerInterface required for this extension to ' .
                'function.'
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
