<?php

declare(strict_types=1);

namespace Acpr\Behat\Expressive\Context\Initializer;

use Acpr\Behat\Expressive\Context\Psr11AwareContext;
use Acpr\Behat\Expressive\Context\Psr11MinkAwareContext;
use Acpr\Behat\Expressive\RuntimeConfigurableKernel;
use Acpr\Behat\Expressive\ServiceContainer\Factory\ZendExpressiveFactory;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer as BehatContextInitializer;
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Session;
use Symfony\Component\HttpKernel\HttpKernelBrowser;

class ContextInitializer implements BehatContextInitializer
{
    /**
     * @var ZendExpressiveFactory
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

    public function __construct(ZendExpressiveFactory $factory, RuntimeConfigurableKernel $kernel, string $minkBasePath)
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