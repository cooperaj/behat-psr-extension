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
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Session;
use Symfony\Component\HttpKernel\HttpKernelBrowser;

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
        if ($context instanceof Psr11AwareContext) {
            $context->setContainer($this->factory->createContainer());
        }

        if ($context instanceof Psr11MinkAwareContext) {
            $container = $this->factory->createContainer();

            $application = $this->factory->createApplication($container);
            $this->kernel->setApplication($application);

            /** @psalm-suppress PossiblyNullArgument */
            $context->setContainer($container);
            $context->setMinkSession(($this->minkSessionFactory)($this->kernel));
        }
    }
}