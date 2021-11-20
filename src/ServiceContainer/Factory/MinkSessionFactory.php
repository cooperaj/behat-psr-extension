<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\ServiceContainer\Factory;

use Acpr\Behat\Psr\RuntimeConfigurableKernel;
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Session;
use Symfony\Component\HttpKernel\HttpKernelBrowser;

class MinkSessionFactory
{
    public function __construct(private string $minkBasePath)
    {
        // until https://github.com/FriendsOfBehat/MinkBrowserKitDriver/pull/2 is fixed this is necessary
        if (null === parse_url($minkBasePath, PHP_URL_PATH)) {
            throw new \RuntimeException(
                'The configured MinkExtension base url must end in a "/" to work successfully'
            );
        }
    }

    public function __invoke(RuntimeConfigurableKernel $kernel): Session
    {
        $client = new HttpKernelBrowser($kernel);
        $driver = new BrowserKitDriver($client, $this->minkBasePath);

        return new Session($driver);
    }
}
