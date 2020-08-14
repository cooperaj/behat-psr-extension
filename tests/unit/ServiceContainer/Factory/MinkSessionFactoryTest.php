<?php

declare(strict_types=1);

namespace TestAcpr\Behat\Psr\ServiceContainer\Factory;

use Acpr\Behat\Psr\RuntimeConfigurableKernel;
use Acpr\Behat\Psr\ServiceContainer\Factory\MinkSessionFactory;
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Session;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelBrowser;

/**
 * Class MinkSessionFactoryTest
 *
 * @package TestAcpr\Behat\Psr\ServiceContainer\Factory
 * @coversDefaultClass \Acpr\Behat\Psr\ServiceContainer\Factory\MinkSessionFactory
 */
class MinkSessionFactoryTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::__invoke
     */
    public function it_creates_a_session_that_wraps_our_runtime_kernel()
    {
        $factory = new MinkSessionFactory('http://localhost');

        // because of protected properties the only way to test our kernel is used is to mock it and
        // see that things get called on it.
        $kernel = $this->prophesize(RuntimeConfigurableKernel::class);
        $kernel->handle(Argument::any(), Argument::any(), Argument::any())
            ->willReturn(new Response('test-worked', Response::HTTP_OK));

        $session = $factory($kernel->reveal());

        $this->assertInstanceOf(Session::class, $session);
        $this->assertInstanceOf(BrowserKitDriver::class, $session->getDriver());

        /** @var BrowserKitDriver $driver */
        $driver = $session->getDriver();
        $this->assertInstanceOf(HttpKernelBrowser::class, $driver->getClient());

        // becomes a bit of an integration test at this point due to aforementioned protected properties
        $session->getDriver()->visit('test');
        $this->assertEquals('test-worked', $session->getPage()->getContent());
    }
}
