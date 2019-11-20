<?php

declare(strict_types=1);

namespace Acpr\Behat\Expressive\Context;

use Psr\Container\ContainerInterface;

interface Psr11AwareContext
{
    public function setContainer(ContainerInterface $container): void;
}