<?php
namespace EacgGmbh\ECSComposer;

/**
 * Application tests
 *
 * @author Anatolii Varanytsia <prizrack13@mail.ru>
 */

class ComposerScannerTest extends \PHPUnit_Framework_TestCase
{
	public function testRunMethod()
	{
		$package = unserialize(file_get_contents(__DIR__ . '/Fixtures/curl_curl'));
		$composerStub = $this->getMock('Composer\Composer');
		$composerStub->expects($this->any())->method('getPackage')->will($this->returnValue($package));
		$composerScannerStub = $this->getMock('\EacgGmbh\ECSComposer\ComposerScanner', array('packageToArray'), array($composerStub));
		$result = $composerScannerStub->run()->result;
		$this->assertEquals($result, array('project' => null, 'module' => 'curl/curl', 'moduleId' => 'composer:curl/curl', 'dependencies' => array(null)));
	}

	public function testPackageToArrayMethod()
	{
		$package = unserialize(file_get_contents(__DIR__ . '/Fixtures/curl_curl'));
		$configStub = $this->getMock('Composer\Config');
		$repositoryManagerStub = $this->getMock('Composer\Repository\RepositoryManager', array(), array(), '', false);
		$repositoryManagerStub->expects($this->any())->method('getLocalRepository')->will($this->returnValue(new \Composer\Repository\ArrayRepository(array())));
		$repositoryManagerStub->expects($this->any())->method('getRepositories')->will($this->returnValue(array()));
		$composerStub = $this->getMock('Composer\Composer');
		$composerStub->expects($this->any())->method('getConfig')->will($this->returnValue($configStub));
		$composerStub->expects($this->any())->method('getRepositoryManager')->will($this->returnValue($repositoryManagerStub));

		$composerScanner = new ComposerScanner($composerStub);
		$result = $composerScanner->packageToArray($package);

		$this->assertEquals($result['name'], 'curl/curl');
		$this->assertEquals($result['key'], 'composer:curl/curl');
		$this->assertEquals($result['description'], 'cURL class for PHP');
		$this->assertEquals($result['private'], 1);
		$this->assertEquals($result['licenses'], array(array('name' => 'MIT')));
		$this->assertEquals($result['homepageUrl'], 'https://github.com/php-mod/curl');
		$this->assertEquals($result['repoUrl'], 'https://github.com/php-mod/curl.git');
		$this->assertEquals($result['versions'], array('1.6.1.0'));
		$this->assertEquals(count($result['dependencies']), 2);
	}

	public function testGetComposerMethod()
	{
		$composerScanner = new ComposerScanner('composer');
		$this->assertEquals($composerScanner->getComposer(), 'composer');
	}

	public function testGetVersionParserMethod()
	{
		$composerScanner = new ComposerScanner('composer');
		$this->assertInstanceOf('Composer\Package\Version\VersionParser', $composerScanner->getVersionParser());
	}

	public function testGetReposMethod()
	{
		$configStub = $this->getMock('Composer\Config');
		$repositoryManagerStub = $this->getMock('Composer\Repository\RepositoryManager', array(), array(), '', false);
		$repositoryManagerStub->expects($this->any())->method('getLocalRepository')->will($this->returnValue(new \Composer\Repository\ArrayRepository(array())));
		$repositoryManagerStub->expects($this->any())->method('getRepositories')->will($this->returnValue(array()));
		$composerStub = $this->getMock('Composer\Composer');
		$composerStub->expects($this->any())->method('getConfig')->will($this->returnValue($configStub));
		$composerStub->expects($this->any())->method('getRepositoryManager')->will($this->returnValue($repositoryManagerStub));
		$composerScanner = new ComposerScanner($composerStub);
		$this->assertInstanceOf('Composer\Repository\CompositeRepository', $composerScanner->getRepos());
	}

	public function testGetPackageMethod()
	{
		$reflectionClass = new \ReflectionClass('EacgGmbh\ECSComposer\ComposerScanner');
		$getPackageMethod = $reflectionClass->getMethod('getPackage');
		$getPackageMethod->setAccessible(true);
		$package = unserialize(file_get_contents(__DIR__ . '/Fixtures/curl_curl'));
		$repos = new \Composer\Repository\CompositeRepository(array($package->getRepository()));
		$composerScanner = new ComposerScanner('composer');
		list($searchPackage, $versions) = $getPackageMethod->invokeArgs($composerScanner, array($repos, $package->getName(), $package->getVersion()));
		$this->assertEquals($package, $searchPackage);
		$this->assertEquals($versions, array('1.6.1' => '1.6.1.0'));
	}
}
