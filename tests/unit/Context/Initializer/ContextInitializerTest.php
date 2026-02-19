<?php

declare(strict_types=1);

namespace TestAcpr\Behat\Psr\Context\Initializer;

use Acpr\Behat\Psr\Context\Initializer\ContextInitializer;
use Acpr\Behat\Psr\Context\Psr11AwareContext;
use Acpr\Behat\Psr\Context\Psr11MinkAwareContext;
use Acpr\Behat\Psr\RuntimeConfigurableKernel;
use Acpr\Behat\Psr\ServiceContainer\Factory\MinkSessionFactoryInterface;
use Acpr\Behat\Psr\ServiceContainer\Factory\PsrFactoryInterface;
use Behat\Mink\Session as MinkSession;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(ContextInitializer::class)]
class ContextInitializerTest extends TestCase
{
    use ProphecyTrait;

    private ?ObjectProphecy $psrFactoryProphecy = null;
    private ?ObjectProphecy $minkSessionFactoryProphecy = null;
    private ?ObjectProphecy $runtimeConfigurableKernelProphecy = null;

    #[\Override]
    public function setUp(): void
    {
        $this->psrFactoryProphecy = $this->prophesize(PsrFactoryInterface::class);
        $this->minkSessionFactoryProphecy = $this->prophesize(MinkSessionFactoryInterface::class);
        $this->runtimeConfigurableKernelProphecy = $this->prophesize(RuntimeConfigurableKernel::class);
    }

    #[Test]
    public function it_correctly_initializes_a_psr11_context(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $applicationProphecy = $this->prophesize(RequestHandlerInterface::class);

        $this->psrFactoryProphecy->createContainer()
            ->willReturn($containerProphecy->reveal());
        $container = $containerProphecy->reveal();
        $this->psrFactoryProphecy->createApplication($container)
            ->willReturn($applicationProphecy->reveal());

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

    #[Test]
    public function it_correctly_initializes_a_psr11_mink_aware_context(): void
    {
        $applicationProphecy = $this->prophesize(RequestHandlerInterface::class);
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $minkSessionProphecy = $this->prophesize(MinkSession::class);

        $container = $containerProphecy->reveal();
        $this->psrFactoryProphecy->createApplication($container)
            ->willReturn($applicationProphecy->reveal());

        $this->psrFactoryProphecy->createContainer()
            ->willReturn($container);

        $this->minkSessionFactoryProphecy->__invoke($this->runtimeConfigurableKernelProphecy->reveal())
            ->willReturn($minkSessionProphecy->reveal());

        $this->runtimeConfigurableKernelProphecy->setApplication($applicationProphecy->reveal())
            ->shouldBeCalled();

        $contextProphecy = $this->prophesize(Psr11MinkAwareContext::class);
        $contextProphecy->setContainer($container)
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

    #[Test]
    public function it_detects_when_a_custom_factory_invalidates_a_container(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $applicationProphecy = $this->prophesize(RequestHandlerInterface::class);
        $contextProphecy = $this->prophesize(Psr11AwareContext::class);

        $psrFactoryMock = new class($containerProphecy, $applicationProphecy) implements PsrFactoryInterface {
            public function __construct(
                private ObjectProphecy $containerProphecy,
                private ObjectProphecy $applicationProphecy
            ) {}

            /**
             * @psalm-param-out \stdClass $container
             */
            public function createApplication(?ContainerInterface &$container = null): RequestHandlerInterface
            {
                $container = new \stdClass(); // why is this possible?
                return $this->applicationProphecy->reveal();
            }

            public function createContainer(): ContainerInterface
            {
                return $this->containerProphecy->reveal();
            }
        };

        $initializer = new ContextInitializer(
            $psrFactoryMock,
            $this->minkSessionFactoryProphecy->reveal(),
            $this->runtimeConfigurableKernelProphecy->reveal()
        );

        $this->expectException(\RuntimeException::class);
        $initializer->initializeContext($contextProphecy->reveal());
    }
}
