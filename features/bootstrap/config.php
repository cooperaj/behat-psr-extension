<?php

declare(strict_types=1);

$aggregator = new \Laminas\ConfigAggregator\ConfigAggregator([
    \Laminas\HttpHandlerRunner\ConfigProvider::class,
    \Mezzio\ConfigProvider::class,
    \Mezzio\Router\ConfigProvider::class,
    \Mezzio\Router\FastRouteRouter\ConfigProvider::class,
]);

return $aggregator->getMergedConfig();
