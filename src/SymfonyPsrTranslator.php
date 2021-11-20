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
    public function __construct(
        private HttpFoundationFactoryInterface $symfonyFactory,
        private HttpMessageFactoryInterface $psrFactory,
    ) {}

    /**
     * Takes a Symfony Http request and returns a Psr request
     *
     * @param HttpFoundationRequest $request
     * @return PsrRequest
     */
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
        if ($cookies !== '') {
            $psrRequest = $psrRequest->withHeader('cookie', $cookies);
        }

        return $psrRequest;
    }

    /**
     * Take a Psr conformant response and return a Symfony response
     *
     * @param PsrResponse $response
     * @return HttpFoundationResponse
     */
    public function translateResponse(PsrResponse $response): HttpFoundationResponse
    {
        return $this->symfonyFactory->createResponse($response);
    }
}