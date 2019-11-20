<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\Context\Initializer;

use Acpr\Behat\Psr\Context\Psr11AwareContext;
use Acpr\Behat\Psr\Context\Psr11MinkAwareContext;
use Acpr\Behat\Psr\RuntimeConfigurableKernel;
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
     * @var string
     */
    private $minkBasePath;
    /**
     * @var RuntimeConfigurableKernel
     */
    private $kernel;

    public function __construct(PsrFactory $factory, RuntimeConfigurableKernel $kernel, string $minkBasePath)
    {
        $this->factory = $factory;
        $this->kernel = $kernel;
        $this->minkBasePath = $minkBasePath;
    }

    public function initializeContext(Context $context): void
    {
        if ($context instanceof Psr11AwareContext) {
            $context->setContainer($this->factory->createContainer());
        }

        if ($context instanceof Psr11MinkAwareContext) {
            $container = $this->factory->createContainer();
            $context->setContainer($container);

            $application = $this->factory->createApplication($container);
            $this->kernel->setApplication($application);

            $client = new HttpKernelBrowser($this->kernel);
            $driver = new BrowserKitDriver($client, $this->minkBasePath);
            $session = new Session($driver);
            $context->setMinkSession($session);
        }
    }
}