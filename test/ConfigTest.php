<?php
require_once '../app/bootstrap.php';

class ConfigTest extends PHPUnit_Framework_TestCase
{
	public function new_config()
	{
		return new Config('config.php');
	}
	
	public function test_config_op()
	{
		@unlink('config.php');
		$config = new Config('config.php');
		$config->addDefault('wener', 'wener is greate','this is my name');
		$this->assertEquals($config['wener'], 'wener is greate');
		$this->assertEquals(1, $config->count());
		$this->assertEquals(true, $config->isChanged());
		
		unset($config);
	}
	/**
	 * @denpend test_config_op
	 */
	public function test_config_save()
	{
		$config = new Config('config.php');
		
		$this->assertEquals($config['wener'], 'wener is greate');
		$this->assertEquals(1, $config->count());
		$this->assertEquals(false, $config->isChanged());
		
		// ensure not effect
		$config->addDefault('wener', 'wener is greate','this is my name');
		
		$this->assertEquals($config['wener'], 'wener is greate');
		$this->assertEquals(1, $config->count());
		$this->assertEquals(false, $config->isChanged());
		
		// first test description
		$this->assertEquals($config->getDescription('wener'), 'this is my name');
		
		$config->addDefault('name', 'wener','name field');
		$this->assertEquals(true, $config->isChanged());
		$config->Save();
		$this->assertEquals(false, $config->isChanged());
		
		$config->addDefault('sex', '女');
		$this->assertEquals(true, $config->isChanged());
		$config->Save();
		$this->assertEquals(false, $config->isChanged());
		
		$config['sex'] = '男';
		$this->assertEquals(true, $config->isChanged());
		$config->Save();
		$this->assertEquals(false, $config->isChanged());
		
		unset($config);
	}
	
	/**
	 * @depend test_config_save
	 */
	public function test_test_value()
	{
		$config = new Config('config.php');
		
		$this->assertEquals($config['name'], 'wener');
		$this->assertEquals($config['sex'], '男');
		
		unset($config);
	}
	
}
