<?php

declare(strict_types=1);

namespace TestAcpr\Behat\Psr\ServiceContainer;

use Acpr\Behat\Psr\ServiceContainer\Extension;
use Acpr\Behat\Psr\ServiceContainer\Factory\PsrDriverFactory;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

#[CoversClass(Extension::class)]
class ExtensionTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function it_returns_a_config_key(): void
    {
        $extension = new Extension();

        $key = $extension->getConfigKey();
        $this->assertEquals('Acpr\Behat\Psr\ServiceContainer', $key);
    }

    #[Test]
    public function it_initialises_a_driver_factory_into_mink(): void
    {
        $minkExtensionProphecy = $this->prophesize(MinkExtension::class);
        $minkExtensionProphecy->getConfigKey()->willReturn('mink');
        $minkExtensionProphecy->registerDriverFactory(Argument::type(PsrDriverFactory::class))
            ->shouldBeCalled();

        $extension = new Extension();

        // urgh. don't you just love a final class without an interface
        $manager = new ExtensionManager([$minkExtensionProphecy->reveal(), $extension]);

        $extension->initialize($manager);
    }

    #[Test]
    public function it_loads_configuration_into_the_container(): void
    {
        // all gross and icky but I have no other way to implement this due to Behat extension architecture
        $containerProphecy = $this->prophesize(ContainerBuilder::class);
        $containerProphecy->fileExists(Argument::type('string'))->willReturn(true);
        $containerProphecy->removeBindings(Argument::type('string'));
        $containerProphecy->setDefinition(Argument::type('string'), Argument::type(Definition::class))
            ->willReturn($this->prophesize(Definition::class)->reveal());

        $containerProphecy->setParameter('acpr.behat.psr.container.file', 'container.php')
            ->shouldBeCalled();
        $containerProphecy->setParameter('acpr.behat.psr.application.file', 'application.php')
            ->shouldBeCalled();

        $extension = new Extension();

        $extension->load(
            $containerProphecy->reveal(),
            [
                'container' => 'container.php',
                'application' => 'application.php'
            ]
        );
    }
}
