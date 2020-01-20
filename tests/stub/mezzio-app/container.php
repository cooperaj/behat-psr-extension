<?php

// Load configuration
$config = (new \Laminas\ConfigAggregator\ConfigAggregator([
    \Laminas\HttpHandlerRunner\ConfigProvider::class,
    \Mezzio\ConfigProvider::class,
    \Mezzio\Router\ConfigProvider::class,
    \Mezzio\Router\FastRouteRouter\ConfigProvider::class,
]))->getMergedConfig();

// Build container
$container = new \Laminas\ServiceManager\ServiceManager();
(new \Laminas\ServiceManager\Config($config['dependencies']))->configureServiceManager($container);

// Inject config
$container->setService('config', $config);

return $container;
