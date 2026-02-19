<?php

declare(strict_types=1);

namespace TestAcpr\Behat\Psr\Context;

use Acpr\Behat\Psr\Context\RuntimeMinkContext;
use Behat\Mink\Mink;
use Behat\Mink\Session as MinkSession;
use Behat\MinkExtension\Context\RawMinkContext;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use ReflectionClass;
use RuntimeException;

#[CoversClass(RuntimeMinkContext::class)]
class RuntimeMinkContextTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function it_defines_a_before_scenario_function(): void
    {
        $reflectionClass = new ReflectionClass(RuntimeMinkContext::class);
        $method = $reflectionClass->getMethod('runtimeMinkSession');

        $this->assertStringContainsString('@BeforeScenario', $method->getDocComment());
    }

    #[Test]
    public function it_correctly_registers_a_new_mink_session_in_a_valid_context_class(): void
    {
        /** @psalm-suppress MissingConstructor */
        $contextStubClass = new class() extends RawMinkContext {
            use RuntimeMinkContext;

            public Mink $mink;
            public int $getMinkCallCount = 0;

            #[\Override]
            public function getMink(): Mink
            {
                $this->getMinkCallCount++;
                return $this->mink;
            }
        };

        $minkSessionProphecy = $this->prophesize(MinkSession::class);

        $minkProphecy = $this->prophesize(Mink::class);
        $minkProphecy->registerSession('psr', $minkSessionProphecy->reveal())
            ->shouldBeCalled();
        $minkProphecy->resetSessions()
            ->shouldBeCalled();
        $contextStubClass->mink = $minkProphecy->reveal();

        $contextStubClass->setMinkSession($minkSessionProphecy->reveal());
        $contextStubClass->runtimeMinkSession();

        $this->assertGreaterThan(0, $contextStubClass->getMinkCallCount);
    }

    #[Test]
    public function it_throws_an_exception_when_not_used_in_a_correct_class(): void
    {
        $contextStubClass = new class() {
            use RuntimeMinkContext;
        };

        $minkSessionProphecy = $this->prophesize(MinkSession::class);

        $contextStubClass->setMinkSession($minkSessionProphecy->reveal());

        $this->expectException(RuntimeException::class);
        $contextStubClass->runtimeMinkSession();
    }

    #[Test]
    public function it_throws_an_exception_when_not_initialized_correctly(): void
    {
        $contextStubClass = new class() extends RawMinkContext {
            use RuntimeMinkContext;
        };

        $this->expectException(RuntimeException::class);
        $contextStubClass->runtimeMinkSession();
    }
}
