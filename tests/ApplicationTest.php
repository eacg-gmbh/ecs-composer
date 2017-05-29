<?php
namespace EacgGmbh\ECSComposer;

/**
 * Application tests
 *
 * @author Anatolii Varanytsia <prizrack13@mail.ru>
 */

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
	public function testRunMethod()
	{
		$configStub = $this->getMock('Composer\Config');
		$configStub->expects($this->any())->method('get')->will($this->returnValue(__DIR__ . '/Fixtures'));
		$composerStub = $this->getMock('Composer\Composer');
		$composerStub->expects($this->any())->method('getConfig')->will($this->returnValue($configStub));
		$composerStub->expects($this->any())->method('getPackage')->will($this->throwException(new \ErrorException('Run scanner')));
		// It catch error and not run next methods

		$reflectionClass = new \ReflectionClass('EacgGmbh\ECSComposer\Application');
		$runMethod = $reflectionClass->getMethod('run');
		$runMethod->setAccessible(true);
		ob_start();
		$runMethod->invokeArgs(new Application(), array($composerStub));
		ob_end_clean();
	}

	/**
	 * @expectedException     ErrorException
	 * @expectedExceptionMessageRegExp /Please provide a 'userName' and 'apiKey'/
	 */
	public function testValidateOptionsMethod()
	{
		$reflectionClass = new \ReflectionClass('EacgGmbh\ECSComposer\Application');
		$validateOptionsMethod = $reflectionClass->getMethod('validateOptions');
		$validateOptionsMethod->setAccessible(true);
		$validateOptionsMethod->invokeArgs(new Application(), array(array()));
	}

	/**
	 * @expectedException     ErrorException
	 * @expectedExceptionMessageRegExp /Please provide a 'project' property/
	 */
	public function testValidateOptionsProjectMethod()
	{
		$reflectionClass = new \ReflectionClass('EacgGmbh\ECSComposer\Application');
		$validateOptionsMethod = $reflectionClass->getMethod('validateOptions');
		$validateOptionsMethod->setAccessible(true);
		$validateOptionsMethod->invokeArgs(new Application(), array(array('userName' => 'userName', 'apiKey' => 'apiKey')));
	}

	public function testPackageInfoMethod()
	{
		$this->assertArrayHasKey('name', Application::packageInfo());
	}

	public function testNameMethod()
	{
		$this->assertEquals(Application::name(), Application::packageInfo()['name']);
	}

	public function testVersionMethod()
	{
		$this->assertEquals(Application::version(), Application::packageInfo()['version']);
	}
}

