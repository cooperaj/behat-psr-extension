<?php

declare(strict_types=1);

namespace TestAcpr\Behat\Psr\Context\Initializer;

use Acpr\Behat\Psr\Context\Initializer\ContextInitializer;
use Acpr\Behat\Psr\Context\Psr11AwareContext;
use Acpr\Behat\Psr\Context\Psr11MinkAwareContext;
use Acpr\Behat\Psr\RuntimeConfigurableKernel;
use Acpr\Behat\Psr\ServiceContainer\Factory\MinkSessionFactory;
use Acpr\Behat\Psr\ServiceContainer\Factory\PsrFactory;
use Behat\Behat\Context\Context;
use Behat\Mink\Session as MinkSession;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ContextInitializerTest
 *
 * @package TestAcpr\Behat\Psr\Context\Initializer
 * @coversDefaultClass \Acpr\Behat\Psr\Context\Initializer\ContextInitializer
 */
class ContextInitializerTest extends TestCase
{
    /**
     * @var ObjectProphecy|PsrFactory
     */
    private $psrFactoryProphecy;

    /**
     * @var ObjectProphecy|MinkSessionFactory
     */
    private $minkSessionFactoryProphecy;

    /**
     * @var ObjectProphecy|RuntimeConfigurableKernel
     */
    private $runtimeConfigurableKernelProphecy;

    public function setUp()
    {
        $this->psrFactoryProphecy = $this->prophesize(PsrFactory::class);
        $this->minkSessionFactoryProphecy = $this->prophesize(MinkSessionFactory::class);
        $this->runtimeConfigurableKernelProphecy = $this->prophesize(RuntimeConfigurableKernel::class);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::initializeContext
     */
    public function it_correctly_initializes_a_psr11_context()
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);

        $this->psrFactoryProphecy->createContainer()
            ->willReturn($containerProphecy->reveal());

        $contextProphecy = $this->prophesize(Psr11AwareContext::class);
        $contextProphecy->setContainer($containerProphecy->reveal())
            ->shouldBeCalled();

        $initializer = new ContextInitializer(
            $this->psrFactoryProphecy->reveal(),
            $this->minkSessionFactoryProphecy->reveal(),
            $this->runtimeConfigurableKernelProphecy->reveal()
        );

        $initializer->initializeContext($contextProphecy->reveal());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::initializeContext
     */
    public function it_correctly_initializes_a_psr11_mink_aware_context()
    {
        $applicationProphecy = $this->prophesize(RequestHandlerInterface::class);
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $minkSessionProphecy = $this->prophesize(MinkSession::class);

        $this->psrFactoryProphecy->createApplication($containerProphecy->reveal())
            ->willReturn($applicationProphecy->reveal());

        $this->psrFactoryProphecy->createContainer()
            ->willReturn($containerProphecy->reveal());

        $this->minkSessionFactoryProphecy->__invoke($this->runtimeConfigurableKernelProphecy->reveal())
            ->willReturn($minkSessionProphecy->reveal());

        $this->runtimeConfigurableKernelProphecy->setApplication($applicationProphecy->reveal())
            ->shouldBeCalled();

        $contextProphecy = $this->prophesize(Psr11MinkAwareContext::class);
        $contextProphecy->setContainer($containerProphecy->reveal())
            ->shouldBeCalled();
        $contextProphecy->setMinkSession($minkSessionProphecy->reveal())
            ->shouldBeCalled();

        $initializer = new ContextInitializer(
            $this->psrFactoryProphecy->reveal(),
            $this->minkSessionFactoryProphecy->reveal(),
            $this->runtimeConfigurableKernelProphecy->reveal()
        );

        $initializer->initializeContext($contextProphecy->reveal());
    }
}
