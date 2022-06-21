<?php

namespace Tests\Unit;

use Exception;
use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Nbj\Config\Reader\ArrayReader;
use org\bovigo\vfs\vfsStreamWrapper;
use Nbj\Config\Contract\ConfigReader;
use org\bovigo\vfs\vfsStreamDirectory;

class ArrayReaderTest extends TestCase
{
    public function setUp(): void
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('configPath'));

        vfsStream::create([
            'test.php' => '<?php return ["key" => "value", "another-key" => ["nested-key" => "nested-value"]];',
        ]);
    }

    /** @test */
    public function it_takes_exception_to_configuration_path_not_existing()
    {
        $reader = null;

        try {
            $reader = new ArrayReader('/this/path/does/not/exist');
        } catch (Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertEquals('Path to configuration files does not exist: /this/path/does/not/exist', $exception->getMessage());
        }

        $this->assertNull($reader);
    }

    /** @test */
    public function it_can_be_instantiated_with_a_correct_path_to_configuration_files()
    {
        $reader = new ArrayReader(vfsStream::url('configPath'));

        $this->assertInstanceOf(ConfigReader::class, $reader);
        $this->assertInstanceOf(ArrayReader::class, $reader);
    }

    /** @test */
    public function it_can_get_values_from_configuration_files()
    {
        $reader = new ArrayReader(vfsStream::url('configPath'));

        $this->assertEquals('value', $reader->get('test.key'));
        $this->assertEquals('nested-value', $reader->get('test.another-key.nested-key'));

        $this->assertEquals([
            'key'         => 'value',
            'another-key' => [
                'nested-key' => 'nested-value',
            ],
        ], $reader->get('test'));
    }

    /** @test */
    public function it_returns_the_default_value_if_key_cannot_be_resolved()
    {
        $reader = new ArrayReader(vfsStream::url('configPath'));

        $this->assertEquals('defaultValue', $reader->get('this.key.does.not.exist', 'defaultValue'));
    }

    /** @test */
    public function it_returns_null_if_config_key_is_an_empty_string()
    {
        $reader = new ArrayReader(vfsStream::url('configPath'));

        $value = $reader->get('');

        $this->assertNull($value);
    }

    /** @test */
    public function it_returns_null_if_config_key_is_not_a_string()
    {
        $reader = new ArrayReader(vfsStream::url('configPath'));

        $value = $reader->get(1);

        $this->assertNull($value);
    }

    /** @test */
    public function it_returns_null_if_config_key_does_not_exist()
    {
        $reader = new ArrayReader(vfsStream::url('configPath'));

        $value = $reader->get('this.key.does.not.exist');

        $this->assertNull($value);
    }

    /** @test */
    public function it_takes_exception_to_setting_a_value_is_key_is_an_empty_string()
    {
        $reader = new ArrayReader(vfsStream::url('configPath'));

        $safetyCheck = true;

        try {
            $reader->set('', 'new-value');

            $safetyCheck = false;
        } catch (Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertEquals('The given key is invalid. It must be a non-empty string', $exception->getMessage());
        }

        $this->assertTrue($safetyCheck);
    }

    /** @test */
    public function it_takes_exception_to_setting_a_value_is_key_is_not_a_string()
    {
        $reader = new ArrayReader(vfsStream::url('configPath'));

        $safetyCheck = true;

        try {
            $reader->set(10, 'new-value');

            $safetyCheck = false;
        } catch (Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertEquals('The given key is invalid. It must be a non-empty string', $exception->getMessage());
        }

        $this->assertTrue($safetyCheck);
    }

    /** @test */
    public function it_can_have_new_values_set_to_it()
    {
        $reader = new ArrayReader(vfsStream::url('configPath'));
        $this->assertNull($reader->get('this.key.is.new'));

        $reader->set('this.key.is.new', 'new-value');
        $this->assertEquals('new-value', $reader->get('this.key.is.new'));
        $this->assertEquals(['key' => ['is' => ['new' => 'new-value']]], $reader->get('this'));

        $reader->set('that', ['key' => ['is' => ['also' => ['new' => 'some-other-new-value']]]]);
        $this->assertEquals('some-other-new-value', $reader->get('that.key.is.also.new'));

        $reader->set('key', 'value');
        $this->assertEquals('value', $reader->get('key'));
    }
}
