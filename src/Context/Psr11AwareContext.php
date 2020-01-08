<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\Context;

use Behat\Behat\Context\Context;
use Psr\Container\ContainerInterface;

interface Psr11AwareContext extends Context
{
    public function setContainer(ContainerInterface $container): void;
}