<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\ServiceContainer\Factory;

use Acpr\Behat\Psr\RuntimeConfigurableKernel;
use Behat\Mink\Session;

interface MinkSessionFactoryInterface
{
    public function __invoke(RuntimeConfigurableKernel $kernel): Session;
}
