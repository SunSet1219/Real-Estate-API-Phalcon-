<?php
error_reporting(E_ALL);

use Phalcon\DI;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Db\Adapter\Pdo\Mysql as Database;
use Phalcon\Mvc\Model\Manager as ModelsManager;
/**
 * Very simple MVC structure
 */

$loader = new Loader();
$loader->registerDirs(array(
	'../app/controllers/',
	'../app/models/',
	'../app/plugins/'
));
$loader->register();

$di = new DI();
$di->set('router', 'Phalcon\Mvc\Router');
$di->set('dispatcher', 'Phalcon\Mvc\Dispatcher');
$di->set('modelsManager', 'Phalcon\Mvc\Model\Manager');
$di->set('modelsMetadata', 'Phalcon\Mvc\Model\Metadata\Memory');
$di->set('response', 'Phalcon\Http\Response');
$di->set('request', 'Phalcon\Http\Request');
$di->set('view', function(){
	$view = new View();
	$view->setViewsDir('../app/views/');
	return $view;
});
$di->set('db', function(){
	return new Database(array(
		"host" => "localhost",
		"username" => "idsrealty",
		"password" => "hQ4i7duD3da2",
		"dbname" => "idsrealty",
		"charset" => 'utf8'
	));
});

$di->set('modelsManager', function() {
	return new ModelsManager();
});

$di->set('metatag', function() {
    return new IzicaMetaTags();
});

try {
	$application = new Application($di);
	echo $application->handle()->getContent();
} catch (Exception $e) {
	echo $e->getMessage();
}
