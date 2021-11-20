<?php

declare(strict_types=1);

namespace TestAcpr\Behat\Psr\Context;

use Acpr\Behat\Psr\Context\RuntimeMinkContext;
use Behat\Mink\Mink;
use Behat\Mink\Session as MinkSession;
use Behat\MinkExtension\Context\RawMinkContext;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use ReflectionClass;
use RuntimeException;

/**
 * Class RuntimeMinkContextTest
 *
 * @package TestAcpr\Behat\Psr\Context
 * @coversDefaultClass \Acpr\Behat\Psr\Context\RuntimeMinkContext
 */
class RuntimeMinkContextTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @covers ::setMinkSession
     */
    public function it_provides_the_ability_to_set_a_mink_session()
    {
        $contextMock = $this->getMockForTrait(RuntimeMinkContext::class);

        $this->assertTrue(
            method_exists($contextMock, 'setMinkSession')
        );
    }

    /**
     * @test
     * @coversNothing
     */
    public function it_defines_a_before_scenario_function()
    {
        $reflectionClass = new ReflectionClass(RuntimeMinkContext::class);
        $method = $reflectionClass->getMethod('runtimeMinkSession');

        $this->assertStringContainsString('@BeforeScenario', $method->getDocComment());
    }

    /**
     * @test
     * @covers ::setMinkSession
     * @covers ::runtimeMinkSession
     */
    public function it_correctly_registers_a_new_mink_session_in_a_valid_context_class()
    {
        $contextStubClass = new class() extends RawMinkContext {
            use RuntimeMinkContext;

            public $mink;
            public $getMinkCallCount = 0;

            public function getMink()
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

    /**
     * @test
     * @covers ::setMinkSession
     * @covers ::runtimeMinkSession
     */
    public function it_throws_an_exception_when_not_used_in_a_correct_class()
    {
        $contextStubClass = new class() {
            use RuntimeMinkContext;
        };

        $minkSessionProphecy = $this->prophesize(MinkSession::class);

        $contextStubClass->setMinkSession($minkSessionProphecy->reveal());

        $this->expectException(RuntimeException::class);
        $contextStubClass->runtimeMinkSession();
    }

    /**
     * @test
     * @covers ::runtimeMinkSession
     */
    public function it_throws_an_exception_when_not_initialized_correctly()
    {
        $contextStubClass = new class() extends RawMinkContext {
            use RuntimeMinkContext;
        };

        $this->expectException(RuntimeException::class);
        $contextStubClass->runtimeMinkSession();
    }
}
