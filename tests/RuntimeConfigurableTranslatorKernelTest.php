<?php

declare(strict_types=1);

namespace TestAcpr\Behat\Expressive;

use Acpr\Behat\Expressive\RuntimeConfigurableKernel;
use Acpr\Behat\Expressive\RuntimeConfigurableTranslatorKernel;
use Acpr\Behat\Expressive\SymfonyPsrTranslator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RuntimeConfigurableTranslatorKernelTest extends TestCase
{
    /** @test */
    public function handles_correctly_when_created_with_application()
    {
        $translatorProphecy = $this->prophesize(SymfonyPsrTranslator::class);
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

        $response = $kernel->handle($requestProphecy->reveal());

        $this->assertInstanceOf(Response::class, $response);
    }

    /** @test */
    public function handles_correctly_when_initialized_with_application()
    {
        $translatorProphecy = $this->prophesize(SymfonyPsrTranslator::class);
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

        $this->assertInstanceOf(RuntimeConfigurableKernel::class, $kernel);
        $kernel->setApplication($applicationProphecy->reveal());

        $response = $kernel->handle($requestProphecy->reveal());

        $this->assertInstanceOf(Response::class, $response);
    }

    /** @test */
    public function throws_exception_when_not_initialized_with_application()
    {
        $translatorProphecy = $this->prophesize(SymfonyPsrTranslator::class);

        $requestProphecy = $this->prophesize(Request::class);

        $kernel = new RuntimeConfigurableTranslatorKernel(
            $translatorProphecy->reveal());

        $this->expectException(\RuntimeException::class);
        $response = $kernel->handle($requestProphecy->reveal());
    }
}
