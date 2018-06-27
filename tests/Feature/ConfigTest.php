<?php

namespace Tests\Feature;

use Exception;
use RuntimeException;
use Nbj\Config\Config;
use PHPUnit\Framework\TestCase;
use Nbj\Config\Contract\ConfigReader;

class ConfigTest extends TestCase
{
    /** @test */
    public function it_takes_exception_if_config_has_not_been_initialized()
    {
        $safetyCheck = true;

        try {
            Config::read('some-config-key');

            $safetyCheck = false;
        } catch (Exception $exception) {
            $this->assertInstanceOf(RuntimeException::class, $exception);
            $this->assertEquals('No ConfigReader has been initialized.', $exception->getMessage());
        }

        $this->assertTrue($safetyCheck);
    }

    /** @test */
    public function it_can_be_initialized_statically()
    {
        $reader = $this->createMock(ConfigReader::class);

        try {
            Config::init($reader);
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }

        // Fake it till we make it
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_get_config_values_statically()
    {
        $reader = $this->createMock(ConfigReader::class);
        $reader->method('get')->willReturn('some-config-value');

        Config::init($reader);

        $this->assertEquals('some-config-value', Config::read('some-config-key'));
    }
}
