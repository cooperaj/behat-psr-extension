<?php

declare(strict_types=1);

namespace TestAcpr\Behat\Psr\ServiceContainer\Factory;

use Acpr\Behat\Psr\ServiceContainer\Factory\PsrFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @coversDefaultClass \Acpr\Behat\Psr\ServiceContainer\Factory\PsrFactory
 */
class PsrFactoryTest extends TestCase
{
    private function createBrokenFactory(): PsrFactory
    {
        return new PsrFactory(
            'tests/stub/broken-app/container.php',
            'tests/stub/broken-app/app.php'
        );
    }

    private function createSubtlyBrokenFactory(): PsrFactory
    {
        return new PsrFactory(
            '',
            'tests/stub/subtly-broken-app/app.php'
        );
    }

    private function createEmbeddedFactory(): PsrFactory
    {
        return new PsrFactory(
            '',
            'tests/stub/embedded-container/app.php'
        );
    }

    private function createMezzioFactory(): PsrFactory
    {
        return new PsrFactory(
            'tests/stub/mezzio-app/container.php',
            'tests/stub/mezzio-app/app.php'
        );
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::createContainer
     */
    public function create_a_container(): void
    {
        $factory = $this->createMezzioFactory();

        $container = $factory->createContainer();
        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::createContainer
     */
    public function throw_exception_if_container_creation_fails(): void
    {
        $factory = $this->createBrokenFactory();

        $this->expectException(\InvalidArgumentException::class);
        $factory->createContainer();
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::createContainer
     * @covers ::createApplication
     */
    public function create_an_application(): void
    {
        $factory = $this->createMezzioFactory();

        $container = $factory->createContainer();
        $application = $factory->createApplication($container);
        $this->assertInstanceOf(RequestHandlerInterface::class, $application);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::createApplication
     */
    public function create_an_application_without_a_supplied_container(): void
    {
        $factory = $this->createEmbeddedFactory();

        $container = null;
        $application = $factory->createApplication($container);
        $this->assertInstanceOf(ContainerInterface::class, $container);
        $this->assertInstanceOf(RequestHandlerInterface::class, $application);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::createApplication
     */
    public function throw_exception_if_application_creation_fails(): void
    {
        $factory = $this->createBrokenFactory();

        $this->expectException(\InvalidArgumentException::class);
        $factory->createApplication();
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::createContainer
     * @covers ::createApplication
     */
    public function throw_exception_when_making_an_application_if_no_container_present_afterwards(): void
    {
        $factory = $this->createSubtlyBrokenFactory();

        $container = null;

        $this->expectException(\RuntimeException::class);
        $factory->createApplication($container);
    }
}
