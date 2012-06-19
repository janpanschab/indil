<?php

use Nette\Application\Routers\Route;


// Load Nette Framework
require LIBS_DIR . '/Nette/loader.php';


// Configure application
$configurator = new Nette\Config\Configurator;

// Enable Nette Debugger for error visualisation & logging
//$configurator->setProductionMode(TRUE);
$configurator->enableDebugger(__DIR__ . '/../log');

// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
	->addDirectory(APP_DIR)
	->addDirectory(LIBS_DIR)
	->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();


// Setup router
$container->router[] = new Route('_media/<type>/<filename>.[!<ext>]', array(
    'module' => 'Admin',
    'presenter' => 'Image',
    'action' => 'default',
    'ext' => 'jpg'
));
$container->router[] = new Route('cms/<presenter>/<action>', array(
    'module' => 'Admin',
    'lang' => 'cs',
    'presenter' => 'Default',
    'action' => 'default'
));

// front
$container->router[] = new Route('[<lang cs|en>/]<section foto-video|photos-videos>/<seoname>', array(
            'module' => 'Front',
            'presenter' => 'Articles',
            'action' => 'detail',
            'lang' => 'cs'
        ));
$container->router[] = new Route('[<lang cs|en>/]<section foto-video|photos-videos>/', array(
            'module' => 'Front',
            'presenter' => 'Articles',
            'action' => 'default',
            'lang' => 'cs'
        ));

$container->router[] = new Route('<lang cs|en>/', array(
            'module' => 'Front',
            'presenter' => 'Homepage',
            'action' => 'default',
            'lang' => 'cs'
        ));
$container->router[] = new Route('[<lang cs|en>/]<seoname>', array(
            'module' => 'Front',
            'presenter' => 'Page',
            'action' => 'default',
            'lang' => 'cs'
        ));
$container->router[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);

// Configure and run the application!
$container->application->run();
