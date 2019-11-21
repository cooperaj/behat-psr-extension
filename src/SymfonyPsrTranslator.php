<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr;

use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;
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
        $psrRequest = $this->psrFactory->createRequest($request);

        /**
         * The translation process does not add a Cookie header and bypasses
         * that using the *Request cookie handling. Which you would - cause it's there.
         *
         * But because the app may be directly accessing headers for cookies (maybe using FigCookies)
         * we should populate that manually.
         */
        $cookies = HeaderUtils::toString($request->cookies->all(), '; ');
        $psrRequest = $psrRequest->withHeader('cookie', $cookies);

        return $psrRequest;
    }

    public function translateResponse(PsrResponse $response): HttpFoundationResponse
    {
        return $this->symfonyFactory->createResponse($response);
    }
}