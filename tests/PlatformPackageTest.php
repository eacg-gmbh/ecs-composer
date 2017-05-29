<?php
namespace EacgGmbh\ECSComposer;

/**
 * PlatformPackage tests
 *
 * @author Anatolii Varanytsia <prizrack13@mail.ru>
 */

class PlatformPackageTest extends \PHPUnit_Framework_TestCase
{
	public function testGetMethod()
	{
		$this->assertEquals(PlatformPackage::get('test'), array());
		$result = PlatformPackage::get('php');
		$this->assertArrayHasKey('licenses', $result);
		$this->assertArrayHasKey('homepageUrl', $result);
		$this->assertArrayHasKey('repoUrl', $result);
	}

	public function testGetPHPDependenciesMethod()
	{
		$this->assertTrue(is_array(PlatformPackage::getPHPDependencies()));
	}

	public function testGetExtensionUrlMethod()
	{
		$this->assertRegExp('/f=ext\/curl/', PlatformPackage::getExtensionUrl('curl'));
	}
}
