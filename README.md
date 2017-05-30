ECS Composer 
=======================

[![Latest Stable Version](https://poser.pugx.org/eacg-gmbh/ecs-composer/version)](https://packagist.org/packages/eacg-gmbh/ecs-composer)
[![License](https://poser.pugx.org/eacg-gmbh/ecs-composer/license)](https://packagist.org/packages/eacg-gmbh/ecs-composer)

Composer module to transfer dependency information to our ECS server. Find the solution at https://ecs-app.eacg.de

Requirements
============

* PHP >= 5.4
* composer/composer >= 1.4
* curl/curl >= 1.6
* league/climate >= 3.2

Installation
============
Run: `composer require eacg-gmbh/ecs-composer`

Add `post-autoload-dump` script to the composer.json file to transfer dependency information right after `composer install`, `composer update` or `composer dumpautoload`:

```
"scripts": {
    "post-autoload-dump": [
        "EacgGmbh\\ECSComposer\\Application::postAutoloadDump"
    ]
}
```

To store your credentials for automated transfer you may create `.ecsrc.json` in your project directory or in your home directory to set credentials globally (not recommended!)

`.ecsrc.json` example:

```
{
  "userName": "UserName",
  "apiKey": "apiKey",
  "url": "url",
  "project": "Project Description"
}

```

Usage
=====

You also may initiate transfer to ECS server manually by executing following command via terminal:
 
```
./vendor/bin/ecs-composer
./vendor/bin/ecs-composer -u userName -k apiKey -p Project 
./vendor/bin/ecs-composer -c config.json
```
```
Usage: ./bin/ecs-composer [-k apiKey, --apiKey apiKey] [-c config, --config config] [--help] [-p project, --project project] [--url url] [-u userName, --userName userName] [-v, --version]

Optional Arguments:
	-u userName, --userName userName
		UserName
	-k apiKey, --apiKey apiKey
		apiKey
	-p project, --project project
		project name
	--url url
		url
	-c config, --config config
		config path
	--help
		Prints a usage statement
	-v, --version
		Prints a version
```
