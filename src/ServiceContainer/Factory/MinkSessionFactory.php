<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\ServiceContainer\Factory;

use Acpr\Behat\Psr\RuntimeConfigurableKernel;
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Session;
use Symfony\Component\HttpKernel\HttpKernelBrowser;

final readonly class MinkSessionFactory implements MinkSessionFactoryInterface
{
    public function __construct(private string $minkBasePath)
    {
    }

    #[\Override]
    public function __invoke(RuntimeConfigurableKernel $kernel): Session
    {
        $client = new HttpKernelBrowser($kernel);
        $driver = new BrowserKitDriver($client, $this->minkBasePath);

        return new Session($driver);
    }
}
