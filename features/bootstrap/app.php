<?php

declare(strict_types=1);

/** @var \Zend\Expressive\Application $app */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

$app = $container->get(\Zend\Expressive\Application::class);
$factory = $container->get(\Zend\Expressive\MiddlewareFactory::class);

$app->get(
    '/',
    function (ServerRequestInterface $request): ResponseInterface {
        $response = new Response();
        $response->getBody()->write('Hello ' . $request->getQueryParams()['name']);

        return $response;
    }
);

$app->pipe(Zend\Expressive\Router\Middleware\RouteMiddleware::class);
$app->pipe(Zend\Expressive\Router\Middleware\DispatchMiddleware::class);

return $app;
