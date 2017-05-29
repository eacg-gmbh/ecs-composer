<?php
namespace EacgGmbh\ECSComposer;

/**
 * PlatformPackage contains platform licenses
 *
 * Usage:
 *
 *     PlatformPackage::get('php');
 *
 * @author Anatolii Varanytsia <prizrack13@mail.ru>
 */
class PlatformPackage
{
	/**
	 * PHP license
	 *
	 * @const array
	 */
	const PHP_LICENSE = array('name' => 'PHP-3.01', 'url' => 'http://php.net/license/3_01.txt');
	/**
	 * PHP repository url
	 *
	 * @const string
	 */
	const PHP_REPO_URL = 'http://git.php.net/?p=php-src.git;a=tree;';
	/**
	 * Platforms
	 *
	 * @var array
	 */
	const PLATFORMS = array(
		'php' => array(
			'licenses' => array(self::PHP_LICENSE),
			'homepageUrl' => 'http://php.net',
			'repoUrl' => self::PHP_REPO_URL,
			// Uncomment it to include PHP extensions
			//'dependencies' => 'getPHPDependencies'
		)
	);
	/**
	 * PHP extension
	 *
	 * @var array
	 */
	const PHP_EXTENSIONS = array(
		'bcmath', 'bz2', 'calendar', 'com_dotnet', 'ctype', 'curl', 'date', 'dba', 'dom', 'enchant', 'exif',
		'fileinfo',	'filter', 'ftp', 'gd', 'gettext', 'gmp', 'hash', 'iconv', 'imap', 'interbase', 'intl', 'json',
		'ldap', 'libxml', 'mbstring', 'mysqli', 'mysqlnd', 'oci8', 'odbc', 'opcache', 'openssl', 'pcntl', 'pcre',
		'pdo', 'pdo_dblib', 'pdo_firebird', 'pdo_mysql', 'pdo_oci', 'pdo_odbc', 'pdo_pgsql', 'pdo_sqlite', 'pgsql',
		'phar', 'posix', 'pspell', 'readline', 'recode', 'reflection', 'session', 'shmop', 'simplexml', 'skeleton',
		'snmp', 'soap', 'sockets', 'spl', 'sqlite3', 'standard', 'sysvmsg', 'sysvsem', 'sysvshm', 'tidy', 'tokenizer',
		'wddx', 'xml', 'xmlreader', 'xmlrpc', 'xmlwriter', 'xsl', 'zend_test', 'zip', 'zlib'
	);

	/**
	 * Return platform information
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	public static function get($name)
	{
		$result = array_key_exists($name, self::PLATFORMS) ? self::PLATFORMS[$name] : array();
		if(isset($result['dependencies'])) {
			$result['dependencies'] = self::$result['dependencies']();
		}
		if(in_array(preg_replace('/^ext-/', '', $name), self::PHP_EXTENSIONS)){
			$result['licenses'] = array(self::PHP_LICENSE);
			$result['repoUrl'] = self::getExtensionUrl($name);
		}
		return $result;
	}

	/**
	 * Return PHP dependecies
	 *
	 * @return array
	 */
	public static function getPHPDependencies()
	{
		return array_map(function($name){
			return array(
				'name' => $name,
				'key' => "composer:{$name}",
				'description' => '',
				'private' => true,
				'licenses' => array(self::PHP_LICENSE),
				'homepageUrl' => '',
				'repoUrl' => self::getExtensionUrl($name),
				'versions' => array(),
				'dependencies' => array()
			);
		}, get_loaded_extensions());
	}

	/**
	 * Return PHP extension repository url
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	public static function getExtensionUrl($name)
	{
		return self::PHP_REPO_URL . 'f=ext/' . preg_replace('/^ext-/', '', $name);
	}
}
