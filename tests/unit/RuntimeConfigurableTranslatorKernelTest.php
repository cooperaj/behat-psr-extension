<?php

declare(strict_types=1);

namespace TestAcpr\Behat\Psr;

use Acpr\Behat\Psr\RuntimeConfigurableTranslatorKernel;
use Acpr\Behat\Psr\SymfonyPsrTranslator;
use Acpr\Behat\Psr\SymfonyPsrTranslatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\{Message\ResponseInterface, Message\ServerRequestInterface, Server\RequestHandlerInterface};
use Symfony\{Component\HttpFoundation\Request, Component\HttpFoundation\Response};

#[CoversClass(RuntimeConfigurableTranslatorKernel::class)]
class RuntimeConfigurableTranslatorKernelTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function handles_correctly_when_created_with_application(): void
    {
        $translatorProphecy = $this->prophesize(SymfonyPsrTranslatorInterface::class);
        $translatorProphecy->translateRequest(Argument::type(Request::class))
            ->willReturn($this->prophesize(ServerRequestInterface::class)->reveal());
        $translatorProphecy->translateResponse(Argument::type(ResponseInterface::class))
            ->willReturn($this->prophesize(Response::class));

        $applicationProphecy = $this->prophesize(RequestHandlerInterface::class);
        $applicationProphecy->handle(Argument::type(ServerRequestInterface::class))
            ->shouldBeCalled();

        $requestProphecy = $this->prophesize(Request::class);

        $kernel = new RuntimeConfigurableTranslatorKernel(
            $translatorProphecy->reveal(),
            $applicationProphecy->reveal());

        $kernel->handle($requestProphecy->reveal());
    }

    #[Test]
    public function handles_correctly_when_initialized_with_application(): void
    {
        $translatorProphecy = $this->prophesize(SymfonyPsrTranslatorInterface::class);
        $translatorProphecy->translateRequest(Argument::type(Request::class))
            ->willReturn($this->prophesize(ServerRequestInterface::class)->reveal());
        $translatorProphecy->translateResponse(Argument::type(ResponseInterface::class))
            ->willReturn($this->prophesize(Response::class));

        $applicationProphecy = $this->prophesize(RequestHandlerInterface::class);
        $applicationProphecy->handle(Argument::type(ServerRequestInterface::class))
            ->shouldBeCalled();

        $requestProphecy = $this->prophesize(Request::class);

        $kernel = new RuntimeConfigurableTranslatorKernel(
            $translatorProphecy->reveal());

        $kernel->setApplication($applicationProphecy->reveal());

        $kernel->handle($requestProphecy->reveal());
    }

    #[Test]
    public function throws_exception_when_not_initialized_with_application(): void
    {
        $translatorProphecy = $this->prophesize(SymfonyPsrTranslatorInterface::class);

        $requestProphecy = $this->prophesize(Request::class);

        $kernel = new RuntimeConfigurableTranslatorKernel(
            $translatorProphecy->reveal());

        $this->expectException(\RuntimeException::class);
        $kernel->handle($requestProphecy->reveal());
    }
}
