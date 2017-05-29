<?php
namespace EacgGmbh\ECSComposer;

/**
 * Scan tests
 *
 * @author Anatolii Varanytsia <prizrack13@mail.ru>
 */

class ScanTest extends \PHPUnit_Framework_TestCase
{
	public function testConstruct()
	{
		$data = array('module' => 'test');
		$scan = new Scan($data);
		$this->assertEquals($data, $scan->data);
	}

	public function testFactoryMethod()
	{
		$data = array('module' => 'test');
		$this->assertEquals($data, Scan::factory($data)->data);
	}

	public function testCreateMethod()
	{
		$RestClientStub = $this->getMockWithoutInvokingTheOriginalConstructor('EacgGmbh\ECSComposer\RestClient');
		$RestClientStub->expects($this->any())->method('post')->will($this->returnValue(array('scanId' => 'scanId')));
		RestClient::$instance = $RestClientStub;
		$this->assertEquals(Scan::create()->id, 'scanId');
	}
}
