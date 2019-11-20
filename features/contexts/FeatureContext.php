<?php

declare(strict_types=1);

namespace Acpr\Behat\Expressive\FeatureContexts;

use Acpr\Behat\Expressive\Context\Psr11MinkAwareContext;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\MinkContext;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Application;

class FeatureContext extends MinkContext implements Psr11MinkAwareContext
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var Session
     */
    private $minkSession;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function setMinkSession(Session $session): void
    {
        $this->minkSession = $session;
    }

    /**
     * @BeforeScenario
     */
    public function configureMinkSession()
    {
        $this->getMink()->registerSession('psr', $this->minkSession);
        $this->getMink()->resetSessions();
    }

    /**
     * @When I go to the injected url :name
     */
    public function iGoToTheInjectedUrl($arg1)
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