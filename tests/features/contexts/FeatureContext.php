<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\FeatureContexts;

use Acpr\Behat\Psr\Context\Psr11MinkAwareContext;
use Acpr\Behat\Psr\Context\RuntimeMinkContext;
use Behat\MinkExtension\Context\MinkContext;
use Laminas\Diactoros\Response;
use Mezzio\Application;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FeatureContext extends MinkContext implements Psr11MinkAwareContext
{
    // this trait implements the methods required by the Psr11MinkAwareContext interface, you don't
    // have to use it in your contexts but you do have to do what it does.
    use RuntimeMinkContext;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * @When I go to the injected url
     */
    public function iGoToTheInjectedUrl()
    {
        // add a new route to the system under test by accessing the container and
        // acting on the application directly.
        $app = $this->container->get(Application::class);
        $app->get('/injection',
            function (ServerRequestInterface $request): ResponseInterface {
                $response = new Response();
                $response->getBody()->write('Injected!');

                return $response;
            }
        );

        $this->visit('/injection');
    }
}