ECS Composer 
=======================

[![Latest Stable Version](https://poser.pugx.org/eacg-gmbh/ecs-composer/version)](https://packagist.org/packages/eacg-gmbh/ecs-composer)
[![License](https://poser.pugx.org/eacg-gmbh/ecs-composer/license)](https://packagist.org/packages/eacg-gmbh/ecs-composer)

Composer module to transfer dependency information to ECS server. https://ecs-app.eacg.de

Requirements
============

* PHP >= 5.4
* composer/composer >= 1.4
* curl/curl >= 1.6
* league/climate >= 3.2

Installation
============
Run: `composer require eacg-gmbh/ecs-composer`

Add `post-autoload-dump` script to the composer.json file to transfer dependency information right after `composer install`, `composer update`, `composer dumpautoload`

```
"scripts": {
    "post-autoload-dump": [
        "EacgGmbh\\ECSComposer\\Application::postAutoloadDump"
    ]
}
```

Create `.ecsrc.json` in project directory to set creadentials for project or in home directory to set credentials globally.

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

Also you can manually run transfer to ECS server.
Just run in terminal: 
```
./bin/ecs-composer
./bin/ecs-composer -u userName -k apiKey -p Project 
./bin/ecs-composer -c config.json
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
