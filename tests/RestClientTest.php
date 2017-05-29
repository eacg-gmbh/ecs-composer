<?php
namespace EacgGmbh\ECSComposer;

/**
 * RestClient tests
 *
 * @author Anatolii Varanytsia <prizrack13@mail.ru>
 */

class RestClientTest extends \PHPUnit_Framework_TestCase
{
	public function testSingleton()
	{
		$this->assertSame(RestClient::Instance(), RestClient::Instance());
	}

	public function testSetupMethod()
	{
		$reflectionClass = new \ReflectionClass('EacgGmbh\ECSComposer\RestClient');
		$optionsProperty = $reflectionClass->getProperty('options');
		$optionsProperty->setAccessible(true);
		RestClient::Instance()->setup(array('url' => 'http://url.com'));
		$this->assertEquals($optionsProperty->getValue(RestClient::Instance()), array('url' => 'http://url.com'));
	}

	public function testPrepareCurlMethod()
	{
		$curlReflectionClass = new \ReflectionClass('Curl\Curl');
		$headersProperty = $curlReflectionClass->getProperty('_headers');
		$headersProperty->setAccessible(true);
		$reflectionClass = new \ReflectionClass('EacgGmbh\ECSComposer\RestClient');
		$prepareCurlMethod = $reflectionClass->getMethod('prepareCurl');
		$prepareCurlMethod->setAccessible(true);
		$curlProperty = $reflectionClass->getProperty('curl');
		$curlProperty->setAccessible(true);
		$prepareCurlMethod->invokeArgs(RestClient::Instance()->setup(array('userName' => 'me')), array());
		$this->assertEquals($headersProperty->getValue($curlProperty->getValue(RestClient::Instance()))['X-User'], 'X-User: me');
	}

	public function testPostMethod()
	{
		$curlStub = $this->getMock('Curl\Curl', array('post', 'isSuccess', 'reset'));
		$curlStub->expects($this->any())->method('post')->will($this->returnValue($this->returnSelf()));
		$curlStub->expects($this->any())->method('isSuccess')->will($this->returnValue(true));
		$curlStub->response = '{"scanId": "scanId"}';
		$reflectionClass = new \ReflectionClass('EacgGmbh\ECSComposer\RestClient');
		$curlProperty = $reflectionClass->getProperty('curl');
		$curlProperty->setAccessible(true);
		$curlProperty->setValue(RestClient::Instance(), $curlStub);
		$this->assertEquals(RestClient::Instance()->post('/success'), array('scanId' => 'scanId'));
	}

	/**
	 * @expectedException     ErrorException
	 * @expectedExceptionCode 0
	 */
	public function testPostMethodError()
	{
		RestClient::Instance()->setup(array('url' => 'http://error.localhost.com'))->post('/error');
	}
}
