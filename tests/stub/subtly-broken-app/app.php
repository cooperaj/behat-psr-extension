<?php

/**
 * This application will create a container as a part of the instantiation of the application
 * but will then change it to a value that the extension can't work with.
 */

$app = require dirname(__DIR__) . '/embedded-container/app.php';

// subtly break things as a runtime user might.
$container = null;

return $app;