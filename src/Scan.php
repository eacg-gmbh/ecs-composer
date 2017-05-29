<?php
namespace EacgGmbh\ECSComposer;

/**
 * Scan model
 *
 * Usage:
 *
 *     Scan::create($data);
 *
 * @author Anatolii Varanytsia <prizrack13@mail.ru>
 */
class Scan
{
	/**
	 * Api path
	 *
	 * @const string
	 */
	const API_PATH = '/api/v1';
	/**
	 * Create path
	 *
	 * @const string
	 */
	const CREATE_PATH = '/scans';
	/**
	 * Id
	 *
	 * @var string
	 */
	public $id;
	/**
	 * data
	 *
	 * @const array
	 */
	public $data;

	/**
	 * Initialize scan object
	 *
	 * @param array $data
	 */
	public function __construct($data)
	{
		$this->data = $data;
	}

	/**
	 * Factory method
	 *
	 * @param array $data
	 *
	 * @return Scan
	 */
	static public function factory($data = array())
	{
		return new self($data);
	}

	/**
	 * Create scan object
	 *
	 * @param array $data
	 *
	 * @return Scan
	 */
	static public function create($data = array())
	{
		return self::factory($data)->_create();
	}

	/**
	 * Send information to server
	 *
	 * @return $this
	 */
	private function _create()
	{
		$response = RestClient::Instance()->post(self::API_PATH . self::CREATE_PATH, $this->data);
		$this->id = $response['scanId'];
		return $this;
	}
}
