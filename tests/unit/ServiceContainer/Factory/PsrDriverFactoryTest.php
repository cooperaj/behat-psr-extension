<?php

declare(strict_types=1);

namespace TestAcpr\Behat\Psr\ServiceContainer\Factory;

use Acpr\Behat\Psr\ServiceContainer\Factory\PsrDriverFactory;
use Behat\Mink\Driver\BrowserKitDriver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

#[CoversClass(PsrDriverFactory::class)]
class PsrDriverFactoryTest extends TestCase
{
    #[Test]
    public function it_is_named_psr(): void
    {
        $factory = new PsrDriverFactory();

        $this->assertEquals('psr', $factory->getDriverName());
    }

    #[Test]
    public function it_does_not_support_javascript(): void
    {
        $factory = new PsrDriverFactory();

        $this->assertFalse($factory->supportsJavascript());
    }

    #[Test]
    public function it_returns_a_configured_browserkit_driver_definition(): void
    {
        $factory = new PsrDriverFactory();
        $definition = $factory->buildDriver([]); // drive does not use configuration

        $this->assertEquals(BrowserKitDriver::class, $definition->getClass());

        $arguments = $definition->getArguments();
        $this->assertCount(2, $arguments);

        $client = $arguments[0];
        $this->assertInstanceOf(Reference::class, $client);
        $this->assertEquals('acpr.behat.psr.client', (string) $client);

        $baseUrl = $arguments[1];
        $this->assertEquals('%mink.base_url%', $baseUrl);
    }
}
