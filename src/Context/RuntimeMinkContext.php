<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\Context;

use Behat\Mink\Session;

trait RuntimeMinkContext
{
    /**
     * @var Session
     */
    private $minkSession;

    public function setMinkSession(Session $session): void
    {
        $this->minkSession = $session;
    }

    /**
     * @BeforeScenario
     */
    public function runtimeMinkSession()
    {
        $this->getMink()->registerSession('psr', $this->minkSession);
        $this->getMink()->resetSessions();
    }
}