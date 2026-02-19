<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr;

use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

interface SymfonyPsrTranslatorInterface
{
    /**
     * Takes a Symfony Http request and returns a Psr request
     *
     * @param HttpFoundationRequest $request
     * @return PsrRequest
     */
    public function translateRequest(HttpFoundationRequest $request): PsrRequest;

    /**
     * Take a Psr conformant response and return a Symfony response
     *
     * @param PsrResponse $response
     * @return HttpFoundationResponse
     */
    public function translateResponse(PsrResponse $response): HttpFoundationResponse;
}