<?php

declare(strict_types=1);

namespace TestAcpr\Behat\Psr;

use Acpr\Behat\Psr\SymfonyPsrTranslator;
use Laminas\Diactoros\{Response as PsrResponse, ServerRequest as PsrRequest};
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\{HttpFoundationFactoryInterface, HttpMessageFactoryInterface};
use Symfony\Component\HttpFoundation\{Request as SymfonyRequest, Response as SymfonyResponse};

/**
 * @coversDefaultClass  \Acpr\Behat\Psr\SymfonyPsrTranslator
 */
class SymfonyPsrTranslatorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @covers ::__construct
     * @covers ::translateRequest
     */
    public function given_a_symfony_request_returns_a_psr_one(): void
    {
        $psrFactoryProphecy = $this->prophesize(HttpMessageFactoryInterface::class);
        $symfonyFactoryProphecy = $this->prophesize(HttpFoundationFactoryInterface::class);

        // choosing not to mock these as they're essentially DTOs
        $symfonyRequest = new SymfonyRequest();
        $psrRequest = new PsrRequest();

        $psrFactoryProphecy->createRequest($symfonyRequest)
            ->willReturn($psrRequest);

        $translator = new SymfonyPsrTranslator(
            $symfonyFactoryProphecy->reveal(),
            $psrFactoryProphecy->reveal()
        );

        $translatedRequest = $translator->translateRequest($symfonyRequest);

        $this->assertInstanceOf(ServerRequestInterface::class, $translatedRequest);
        $this->assertArrayNotHasKey('cookie', $translatedRequest->getHeaders());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::translateRequest
     */
    public function given_a_symfony_request_returns_a_psr_one_with_cookie_header(): void
    {
        $psrFactoryProphecy = $this->prophesize(HttpMessageFactoryInterface::class);
        $symfonyFactoryProphecy = $this->prophesize(HttpFoundationFactoryInterface::class);

        // choosing not to mock these as they're essentially DTOs
        $symfonyRequest = new SymfonyRequest();
        $symfonyRequest->cookies->set('testcookie', 'testcookie-value');

        $psrRequest = new PsrRequest();

        $psrFactoryProphecy->createRequest($symfonyRequest)
            ->willReturn($psrRequest);

        $translator = new SymfonyPsrTranslator(
            $symfonyFactoryProphecy->reveal(),
            $psrFactoryProphecy->reveal()
        );

        $translatedRequest = $translator->translateRequest($symfonyRequest);

        $this->assertInstanceOf(ServerRequestInterface::class, $translatedRequest);
        $this->assertArrayHasKey('cookie', $translatedRequest->getHeaders());
        $this->assertStringContainsString('testcookie', $translatedRequest->getHeaderLine('cookie'));
        $this->assertStringContainsString('testcookie-value', $translatedRequest->getHeaderLine('cookie'));
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::translateResponse
     */
    public function given_a_psr_response_returns_a_symfony_one(): void
    {
        $psrFactoryProphecy = $this->prophesize(HttpMessageFactoryInterface::class);
        $symfonyFactoryProphecy = $this->prophesize(HttpFoundationFactoryInterface::class);

        // choosing not to mock these as they're essentially DTOs
        $symfonyResponse = new SymfonyResponse();
        $psrResponse = new PsrResponse();

        $symfonyFactoryProphecy->createResponse($psrResponse)
            ->willReturn($symfonyResponse);

        $translator = new SymfonyPsrTranslator(
            $symfonyFactoryProphecy->reveal(),
            $psrFactoryProphecy->reveal()
        );

        $translatedResponse = $translator->translateResponse($psrResponse);

        $this->assertInstanceOf(SymfonyResponse::class, $translatedResponse);
    }
}
