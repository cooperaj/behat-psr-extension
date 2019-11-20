<?php

declare(strict_types=1);

namespace Acpr\Behat\Expressive;

use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

interface RuntimeConfigurableKernel extends HttpKernelInterface
{
    public function setApplication(RequestHandlerInterface $application): void;
}