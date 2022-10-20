<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var \Mezzio\Application $app */
$app = $container->get(\Mezzio\Application::class);

$app->get(
    '/',
    function (ServerRequestInterface $request): ResponseInterface {
        $response = new \Laminas\Diactoros\Response();
        $response->getBody()->write('Hello ' . (string)$request->getQueryParams()['name']);

        return $response;
    }
);

$app->pipe(\Mezzio\Router\Middleware\RouteMiddleware::class);
$app->pipe(\Mezzio\Router\Middleware\DispatchMiddleware::class);

return $app;
