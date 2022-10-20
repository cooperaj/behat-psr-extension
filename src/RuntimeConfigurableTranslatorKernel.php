<?php

declare(strict_types=1);

namespace Acpr\Behat\Psr;

use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Adapts an arbitrary PSR-7 callable into a Symfony HttpKernel
 */
final class RuntimeConfigurableTranslatorKernel implements HttpKernelInterface, RuntimeConfigurableKernel
{
    public function __construct(
        readonly private SymfonyPsrTranslator $translator,
        private ?RequestHandlerInterface $application = null,
    ) {}

    public function setApplication(RequestHandlerInterface $application): void
    {
        $this->application = $application;
    }

    /**
     * @param Request $request
     * @param int $type
     * @param bool $catch
     * @return Response
     *
     *
     * @psalm-suppress DeprecatedConstant TODO remove when ongoing PHP 7 support is dropped
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true): Response
    {
        if (is_null($this->application)) {
            throw new \RuntimeException('PSR7 Application not passed to constructor. Please ensure your ' .
                'Context implements Psr11MinkAwareContext correctly (if using Mink), or you have supplied a ' .
                'PSR7 application to the ' . __CLASS__ . ' constructor');
        }

        return $this->translator->translateResponse(
            $this->application->handle(
                $this->translator->translateRequest($request)
            )
        );
    }
}
