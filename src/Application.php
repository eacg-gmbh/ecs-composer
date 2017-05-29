<?php
namespace EacgGmbh\ECSComposer;

use Symfony\Component\Console\Input\ArrayInput;
use Composer\Json\JsonFile;
use Composer\Script\Event;

/**
 * Application
 *
 * Usage:
 *
 *     $application = new Application();
 *     $application->runCli(); or $application->run($composer, $options);
 *
 * @author Anatolii Varanytsia <prizrack13@mail.ru>
 */
class Application
{
	/**
	 * Run cli application
	 */
	public function runCli()
	{
		$climate = new \League\CLImate\CLImate;
		$climate->arguments->add(array(
			'userName' => array(
				'prefix'       => 'u',
				'longPrefix'   => 'userName',
				'description'  => 'UserName',
				'defaultValue' => null,
			),
			'apiKey' => array(
				'prefix'       => 'k',
				'longPrefix'   => 'apiKey',
				'description'  => 'apiKey',
				'defaultValue' => null,
			),
			'project' => array(
				'prefix'       => 'p',
				'longPrefix'   => 'project',
				'description'  => 'project name',
				'defaultValue' => null,
			),
			'url' => array(
				'longPrefix'   => 'url',
				'description'  => 'url',
				'defaultValue' => null,
			),
			'config' => array(
				'prefix'       => 'c',
				'longPrefix'   => 'config',
				'description'  => 'config path',
				'defaultValue' => null,
			),
			'help' => array(
				'longPrefix'  => 'help',
				'description' => 'Prints a usage statement',
				'noValue'     => true,
			),
			'version' => array(
				'prefix'       => 'v',
				'longPrefix'  => 'version',
				'description' => 'Prints a version',
				'noValue'     => true,
			)
		));
		$climate->description('Composer module to transfer dependency information to ECS server.');
		$climate->arguments->parse();
		$options = array();
		foreach($climate->arguments->all() as $argument){
			if ($argument->value() !== null) {
				$options[$argument->name()] = $argument->value();
			}
		}
		if ($climate->arguments->defined('help')){
			$climate->usage();
		} elseif($climate->arguments->defined('version')) {
			$climate->out(self::version());
		} else {
			$application = new \Composer\Console\Application();
			$application->setAutoExit(false);
			$application->run(new ArrayInput(array('--version')));
			$composer = $application->getComposer();
			$this->run($composer, $options);
		}
	}

	/**
	 * postAutoloadDump event
	 *
	 * @param Event $event
	 */
	public static function postAutoloadDump(Event $event)
	{
		$composer = $event->getComposer();
		$vendorDir = $composer->getConfig()->get('vendor-dir');
		require $vendorDir . '/autoload.php';

		(new self())->run($composer);
	}

	/**
	 * Run application
	 *
	 * @param Composer $composer
	 * @param array $options
	 */
	protected function run($composer, $options = array())
	{
		Config::Instance()->load(isset($options['config']) ? $options['config']: dirname($composer->getConfig()->get('vendor-dir')));
		$options = array_merge(Config::Instance()->get(), $options);
		$this->validateOptions($options);
		RestClient::Instance()->setup($options);
		$composerScanner = new ComposerScanner($composer);
		try {
			$scan = Scan::create($composerScanner->run($options)->result);
			echo "ecs-composer successfully transferred scan to server: scanId => {$scan->id}\n";
		} catch(\ErrorException $error){
			echo "ecs-composer error transferring scan: " . $error->getMessage() . "\n";
		}
	}

	/**
	 * Validate options
	 *
	 * @param array $options
	 * @throws \ErrorException
	 */
	protected function validateOptions($options = array())
	{
		if (!$options['userName'] || !$options['apiKey']) {
			throw new \ErrorException('Please provide a \'userName\' and \'apiKey\' property in credentials file(\'' . Config::FILE_NAME . '\').');
		}

		if (!$options['project']) {
			throw new \ErrorException('Please provide a \'project\' property in credentials file(\'' . Config::FILE_NAME . '\').');
		}
	}

	/**
	 * package information
	 *
	 * @return array
	 */
	static public function packageInfo()
	{
		static $package_info = null;
		if($package_info === null) {
			$package_info = (new JsonFile(__DIR__ . '/../composer.json'))->read();
		}
		return $package_info;
	}

	/**
	 * Return name of package
	 *
	 * @return string
	 */
	static public function name()
	{
		return self::packageInfo()['name'];
	}

	/**
	 * Return version of package
	 *
	 * @return string
	 */
	static public function version()
	{
		return self::packageInfo()['version'];
	}
}
