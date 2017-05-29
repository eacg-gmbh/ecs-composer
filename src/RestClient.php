<?php
namespace EacgGmbh\ECSComposer;

/**
 * RestClient. Wrapper under curl library
 *
 * Usage:
 *
 *     RestClient::Instance()->setup($options)->post($path, $data);
 *
 * @author Anatolii Varanytsia <prizrack13@mail.ru>
 */
class RestClient
{
	/**
	 * Curl
	 *
	 * @var Curl
	 */
	private $curl;
	/**
	 * options
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Singleton instance
	 *
	 * @var RestClient
	 */
	static $instance = null;

	/**
	 * Return singleton instance
	 *
	 * @return RestClient
	 */
	public static function Instance()
	{
		if (self::$instance === null) {
			self::$instance = new RestClient();
		}
		return self::$instance;
	}

	/**
	 * Setup RestClient
	 *
	 * @param array $options
	 *
	 * @return $this
	 */
	public function setup($options)
	{
		$this->options = $options;
		return $this;
	}

	/**
	 * Reset curl and set headers
	 */
	private function prepareCurl()
	{
		$this->curl->reset();
		$this->curl->setUserAgent($this->options['userAgent']);
		$this->curl->setHeader('Content-Type', 'application/json');
		$this->curl->setHeader('X-ApiKey', $this->options['apiKey']);
		$this->curl->setHeader('X-User', $this->options['userName']);
	}

	/**
	 * Make post request
	 *
	 * @param string $path
	 * @param array $data
	 *
	 * @throws \ErrorException
	 * @return string
	 */
	public function post($path, $data = [])
	{
		$this->prepareCurl();
		$data_string = json_encode($data);
		$this->curl->setHeader('Content-Length', strlen($data_string));
		$this->curl->post($this->options['url'] . $path, $data_string);
		if ($this->curl->error || !$this->curl->isSuccess()) {
			throw new \ErrorException("{$this->curl->error_message}\n{$this->curl->response}");
		} else {
			$response = json_decode($this->curl->response, true);
			if(isset($response['info'])) {
				echo $response['info'], "\n";
			}
			return $response;
		}
	}

	/**
	 * Make it singleton
	 */
	private function __construct()
	{
		$this->curl = new \Curl\Curl();
	}
}
