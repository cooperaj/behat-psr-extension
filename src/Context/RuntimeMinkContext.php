<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr\Context;

use Behat\Mink\Session as MinkSession;
use Behat\MinkExtension\Context\RawMinkContext;
use RuntimeException;

trait RuntimeMinkContext
{
    private ?MinkSession $minkSession = null;

    public function setMinkSession(MinkSession $session): void
    {
        $this->minkSession = $session;
    }

    /**
     * @BeforeScenario
     */
    public function runtimeMinkSession()
    {
        if (! $this instanceof RawMinkContext) {
            throw new RuntimeException(
                'The \Acpr\Behat\Psr\Context\RuntimeMinkContext trait can only be used by a context that ' .
                 'extends \Behat\MinkExtension\Context\RawMinkContext'
            );
        }

        if ($this->minkSession === null) {
            throw new RuntimeException(
                'The context has not been initialized correctly, please ensure it implements ' .
                '\Acpr\Behat\Psr\Context\Psr11MinkAwareContext'
            );
        }

        $this->getMink()->registerSession('psr', $this->minkSession);
        $this->getMink()->resetSessions();
    }
}
