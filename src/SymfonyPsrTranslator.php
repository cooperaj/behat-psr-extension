<?php

declare(strict_types=1);

namespace Acpr\Behat\Expressive;

use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class SymfonyPsrTranslator
{
    /**
     * @var HttpFoundationFactoryInterface
     */
    private $symfonyFactory;
    /**
     * @var HttpMessageFactoryInterface
     */
    private $psrFactory;

    public function __construct(
        HttpFoundationFactoryInterface $symfonyFactory,
        HttpMessageFactoryInterface $psrFactory
    )
    {
        $this->symfonyFactory = $symfonyFactory;
        $this->psrFactory = $psrFactory;
    }

    public function translateRequest(HttpFoundationRequest $request): PsrRequest
    {
        return $this->psrFactory->createRequest($request);
    }

    public function translateResponse(PsrResponse $response): HttpFoundationResponse
    {
        return $this->symfonyFactory->createResponse($response);
    }
}