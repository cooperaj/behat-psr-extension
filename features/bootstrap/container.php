<?php

declare(strict_types=1);

// Load configuration
$config = require __DIR__ . '/config.php';

// Build container
$container = new \Laminas\ServiceManager\ServiceManager();
(new \Laminas\ServiceManager\Config($config['dependencies']))->configureServiceManager($container);

// Inject config
$container->setService('config', $config);

return $container;
