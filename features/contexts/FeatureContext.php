<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\FeatureContexts;

use Acpr\Behat\Psr\Context\Psr11MinkAwareContext;
use Acpr\Behat\Psr\Context\RuntimeMinkContext;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\MinkContext;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Application;

class FeatureContext extends MinkContext implements Psr11MinkAwareContext
{
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