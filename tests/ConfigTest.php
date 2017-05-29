<?php
namespace EacgGmbh\ECSComposer;

/**
 * Config tests
 *
 * @author Anatolii Varanytsia <prizrack13@mail.ru>
 */

class ConfigTest extends \PHPUnit_Framework_TestCase
{
	public function setUp(){
		Config::$instance = null;
	}

	public function testSingleton()
	{
		$this->assertSame(Config::Instance(), Config::Instance());
	}

	public function testGetDefaultOptionsMethod()
	{
		$this->assertArrayHasKey('userName', Config::getDefaultOptions());
	}

	public function testGetMethod()
	{
		$this->assertEquals(Config::Instance()->get(), array());
		$this->assertEquals(Config::Instance()->get('userName'), null);
	}

	public function testLoadMethod()
	{
		$this->assertEquals(Config::Instance()->get(), array());
		Config::Instance()->load(__DIR__ . '/Fixtures');
		$this->assertEquals(Config::Instance()->get(), array('project' => '', 'userName' => 'UserName', 'apiKey' => 'apiKey', 'url' => 'url', 'userAgent' => Application::name() . '/' . Application::version()));
		Config::Instance()->load(__DIR__ . '/Fixtures/conf.json');
		$this->assertEquals(Config::Instance()->get('userName'), 'UserNameConf');
	}
}
