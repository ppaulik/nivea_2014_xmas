<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->enableDebugger(__DIR__ . '/log');
$configurator->setTempDirectory(__DIR__ . '/temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$environment = (Nette\Configurator::detectDebugMode('127.0.0.1') or (PHP_SAPI == 'cli' && Nette\Utils\Strings::startsWith(getHostByName(getHostName()), "192.168.")))
    ? $configurator::DEVELOPMENT
    : $configurator::PRODUCTION;

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . "/config/config.$environment.neon");

$container = $configurator->createContainer();
\CmsModule\Doctrine\ToManyContainer::register();
return $container;
