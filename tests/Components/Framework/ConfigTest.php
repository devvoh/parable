<?php

namespace Parable\Tests\Components\Framework;

class ConfigTest extends \Parable\Tests\Components\Framework\Base
{
    /** @var \Parable\Framework\Config */
    protected $config;

    /** @var \Parable\Tests\TestClasses\Config1 */
    protected $config1;

    /** @var \Parable\Tests\TestClasses\Config2 */
    protected $config2;

    /** @var \Parable\Tests\TestClasses\Config3DuplicateSortOrder */
    protected $config3DuplicateSortOrder;

    protected function setUp()
    {
        parent::setUp();

        $this->config = \Parable\DI\Container::get(\Parable\Framework\Config::class);

        $this->config1                   = new \Parable\Tests\TestClasses\Config1();
        $this->config2                   = new \Parable\Tests\TestClasses\Config2();
        $this->config3DuplicateSortOrder = new \Parable\Tests\TestClasses\Config3DuplicateSortOrder();
    }

    public function testLoadFailsSilently()
    {
        $this->assertSame($this->config, $this->config->load());
    }

    public function testGetConfig()
    {
        $this->config->addConfig($this->config1);
        $this->assertSame("primary value", $this->config->get('setting'));
    }

    public function testGetConfigNonExistingValueReturnsNull()
    {
        $this->config->addConfig($this->config1);
        $this->assertNull(null, $this->config->get('setting_does_not_exist'));
    }

    public function testGetNestedConfigValues()
    {
        $this->config->addConfig($this->config1);
        $this->assertSame(
            ['values' => ['rock' => ['so' => ['much' => true]]]],
            $this->config->get('nested')
        );
        $this->assertSame(
            ['rock' => ['so' => ['much' => true]]],
            $this->config->get('nested.values')
        );
        $this->assertSame(
            ['so' => ['much' => true]],
            $this->config->get('nested.values.rock')
        );
        $this->assertSame(
            ['much' => true],
            $this->config->get('nested.values.rock.so')
        );
        $this->assertTrue($this->config->get('nested.values.rock.so.much'));
    }

    public function testGetConfigSetsDataBasedOnAddConfigTiming()
    {
        $this->config->addConfig($this->config1);
        $this->assertSame("primary value", $this->config->get('setting'));

        $this->config->addConfig($this->config2);
        $this->assertSame("secondary value", $this->config->get('setting'));
    }

    public function testGetConfigSetsDataBasedOnSortOrder()
    {
        $this->config->addConfigs([
            $this->config1,
            $this->config2,
        ]);

        $this->config->addConfig($this->config2);
        $this->assertSame("secondary value", $this->config->get('setting'));
    }

    public function testAddConfigsThrowsExceptionOnDuplicateSortOrder()
    {
        $this->expectException(\Parable\Framework\Exception::class);
        $this->expectExceptionMessage(
            "Sort order duplication by 'Parable\Tests\TestClasses\Config3DuplicateSortOrder'"
        );

        $this->config->addConfigs([
            $this->config1,
            $this->config3DuplicateSortOrder,
        ]);
    }

    public function testAddConfigsThrowsExceptionOnWrongClass()
    {
        $this->expectException(\Parable\Framework\Exception::class);
        $this->expectExceptionMessage(
            "'stdClass' does not implement '\Parable\Framework\Config\Base'"
        );

        $this->config->addConfigs([
            $this->config1,
            new \stdClass(),
        ]);
    }
}
