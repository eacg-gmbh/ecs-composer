<?php
namespace EacgGmbh\ECSComposer;

use Composer\DependencyResolver\Pool;
use Composer\DependencyResolver\DefaultPolicy;
use Composer\Repository\RepositoryInterface;
use Composer\Package\Version\VersionParser;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PlatformRepository;

/**
 * Composer scanner based on composer library
 *
 * Usage:
 *
 *     $composer_scanner = new ComposerScanner($composer);
 *     $composer_scanner->run($options)->result;
 *
 * @author Anatolii Varanytsia <prizrack13@mail.ru>
 */
class ComposerScanner
{
	/**
	 * Composer
	 *
	 * @var Composer
	 */
	private $composer;
	/**
	 * VersionParser
	 *
	 * @var VersionParser
	 */
	private $versionParser;
	/**
	 * All repositories
	 *
	 * @var RepositoryInterface
	 */
	private $repos;
	/**
	 * Result of scan
	 *
	 * @var array
	 */
	public $result;

	/**
	 * @param composer $composer
	 */
	public function __construct($composer)
	{
		$this->composer = $composer;
		$this->versionParser = new VersionParser;
	}

	/**
	 * Run parser
	 *
	 * @param array $options
	 *
	 * @return $this
	 */
	public function run($options = array())
	{
		$package = $this->getComposer()->getPackage();
		$this->result = array(
			'project' => $options['project'],
			'module' => $package->getName(),
			'moduleId' => 'composer:' . $package->getName(),
			'dependencies' => array($this->packageToArray($package))
		);
		return $this;
	}

	/**
	 * Convert package to array
	 *
	 * @param package $package
	 *
	 * @return array
	 */
	public function packageToArray($package)
	{
		$result = array(
			'name' => $package->getName(),
			'key' => "composer:{$package->getName()}",
			'description' => $package->getDescription(),
			'private' => true,
			'licenses' => array_map(function($license){
				return array('name' => $license);
			}, $package->getLicense()),
			'homepageUrl' => $package->getHomepage(),
			'repoUrl' => $package->getSourceUrl(),
			'versions' => array($package->getVersion()),
			'dependencies' => array(),
		);

		foreach($package->getRequires() as $requireName => $require) {
			list($dependencyPackage, $versions) = $this->getPackage($this->getRepos(), $requireName, $require->getPrettyConstraint() === 'self.version' ? $require->getConstraint() : $require->getPrettyConstraint());
			if(is_object($dependencyPackage)) {
				array_push($result['dependencies'], $this->packageToArray($dependencyPackage));
			}
		}

		if ($package->isPlatform()){
			$result = array_merge($result, PlatformPackage::get($package->getName()));
		}
		return $result;
	}

	/**
	 * @return composer
	 */
	public function getComposer()
	{
		return $this->composer;
	}

	/**
	 * @return VersionParser
	 */
	public function getVersionParser()
	{
		if($this->versionParser === null) {
			$this->versionParser = new VersionParser();
		}
		return $this->versionParser;
	}

	/**
	 * Return all repositories
	 *
	 * @return RepositoryInterface
	 */
	public function getRepos()
	{
		if($this->repos === null) {
			$platformOverrides = $this->getComposer()->getConfig()->get('platform') ?: array();
			$platformRepo = new PlatformRepository(array(), $platformOverrides);
			$localRepo = $this->composer->getRepositoryManager()->getLocalRepository();
			$installedRepo = new CompositeRepository(array($localRepo, $platformRepo));
			$this->repos = new CompositeRepository(array_merge(array($installedRepo), $this->getComposer()->getRepositoryManager()->getRepositories()));
		}
		return $this->repos;
	}

	/**
	 * finds a package by name and version if provided
	 *
	 * @param  RepositoryInterface $repos
	 * @param  string $name
	 * @param  ConstraintInterface|string $version
	 * @throws \InvalidArgumentException
	 * @return array                      array(CompletePackageInterface, array of versions)
	 */
	protected function getPackage(RepositoryInterface $repos, $name, $version = null)
	{
		$name = strtolower($name);
		$constraint = is_string($version) ? $this->getVersionParser()->parseConstraints($version) : $version;

		$policy = new DefaultPolicy();
		$pool = new Pool('dev');
		$pool->addRepository($repos);

		$matchedPackage = null;
		$versions = array();
		$matches = $pool->whatProvides($name, $constraint);
		foreach($matches as $index => $package) {
			// skip providers/replacers
			if($package->getName() !== $name) {
				unset($matches[$index]);
				continue;
			}

			// select an exact match if it is in the installed repo and no specific version was required
			if(null === $version && $repos->hasPackage($package)) {
				$matchedPackage = $package;
			}

			$versions[$package->getPrettyVersion()] = $package->getVersion();
			$matches[$index] = $package->getId();
		}

		// select preferred package according to policy rules
		if(!$matchedPackage && $matches && $preferred = $policy->selectPreferredPackages($pool, array(), $matches)) {
			$matchedPackage = $pool->literalToPackage($preferred[0]);
		}

		return array($matchedPackage, $versions);
	}
}
