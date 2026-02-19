<?php

declare(strict_types=1);

namespace TestAcpr\Behat\Psr;

use Acpr\Behat\Psr\SymfonyPsrTranslator;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Laminas\Diactoros\{Response as PsrResponse, ServerRequest as PsrRequest};
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\{HttpFoundationFactoryInterface, HttpMessageFactoryInterface};
use Symfony\Component\HttpFoundation\{Request as SymfonyRequest, Response as SymfonyResponse};

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(SymfonyPsrTranslator::class)]
class SymfonyPsrTranslatorTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
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

        $this->assertArrayNotHasKey('cookie', $translatedRequest->getHeaders());
    }

    #[Test]
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

        $this->assertArrayHasKey('cookie', $translatedRequest->getHeaders());
        $this->assertStringContainsString('testcookie', $translatedRequest->getHeaderLine('cookie'));
        $this->assertStringContainsString('testcookie-value', $translatedRequest->getHeaderLine('cookie'));
    }

    #[Test]
    #[DoesNotPerformAssertions]
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

        $translator->translateResponse($psrResponse);
    }
}
