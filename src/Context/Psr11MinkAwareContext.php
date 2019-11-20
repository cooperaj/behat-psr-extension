<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\Context;

use Behat\Mink\Session;
use Behat\MinkExtension\Context\MinkAwareContext;
use Psr\Container\ContainerInterface;

interface Psr11MinkAwareContext extends MinkAwareContext
{
    public function setContainer(ContainerInterface $container): void;
    public function setMinkSession(Session $session): void;
}