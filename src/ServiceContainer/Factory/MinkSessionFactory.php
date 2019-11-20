<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\ServiceContainer\Factory;

use Acpr\Behat\Psr\RuntimeConfigurableKernel;
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Session;
use Symfony\Component\HttpKernel\HttpKernelBrowser;

class MinkSessionFactory
{
    /**
     * @var string
     */
    private $minkBasePath;

    public function __construct(string $minkBasePath)
    {
        $this->minkBasePath = $minkBasePath;
    }

    public function __invoke(RuntimeConfigurableKernel $kernel): Session
    {
        $client = new HttpKernelBrowser($kernel);
        $driver = new BrowserKitDriver($client, $this->minkBasePath);

        return new Session($driver);
    }
}