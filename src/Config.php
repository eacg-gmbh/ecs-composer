<?php
namespace EacgGmbh\ECSComposer;
use Composer\Json\JsonFile;

/**
 * Config is the container to get config params
 *
 * Usage:
 *
 *     Config::Instance()->load($path);
 *     Config::Instance()->get('userName');
 *     Config::Instance()->get();
 *
 * @author Anatolii Varanytsia <prizrack13@mail.ru>
 */
class Config
{
	/**
	 * File name
	 *
	 * @const string
	 */
	const FILE_NAME = '/.ecsrc.json';
	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * Singleton instance
	 *
	 * @var RestClient
	 */
	static $instance = null;

	/**
	 * @param string $key The name of the key
	 *
	 * @return mixed
	 */
	public function get($key = null)
	{
		if($key) {
			return isset($this->data[$key]) ? $this->data[$key] : null;
		} else {
			return $this->data;
		}
	}

	/**
	 * Return singleton instance
	 *
	 * @return Config
	 */
	public static function Instance()
	{
		if (self::$instance === null) {
			self::$instance = new Config();
		}
		return self::$instance;
	}

	/**
	 * Return default options
	 *
	 * @return array
	 */
	public static function getDefaultOptions()
	{
		return array(
			'url' => 'https://ecs-app.eacg.de',
			'project' => '',
			'apiKey' => '',
			'userName' => '',
			'userAgent' => Application::name() . '/' . Application::version()
		);
	}

	/**
	 * Load config from files
	 *
	 * @return $this
	 */
	public function load($path = null)
	{
		$paths = array_filter(
			array_map(
				function($path){
					if(is_dir($path) && file_exists($path . self::FILE_NAME))
					{
						return $path . self::FILE_NAME;
					} elseif(is_file($path) && file_exists($path)) {
						return $path;
					}
					return null;
				},
				array_merge(is_array($path) ? realpath($path) : array(realpath($path)), array($_SERVER['HOME']))
			)
		);
		if (!empty($paths)) {
			$this->data = array_merge(self::getDefaultOptions(), (new JsonFile(reset($paths)))->read());
		}
		return $this;
	}

	/**
	 * Make it singleton
	 */
	private function __construct()
	{
	}
}
