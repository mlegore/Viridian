<?php

session_start();
$start = microtime(true);
$_POST['username'] = 'mlegore';
$_POST['password'] = 'coolbeans';

//Load the base classes required to run
require_once 'base/Base.php';
require_once 'base/common.php';
require_once 'base/App.php';
require_once 'base/Loader.php';

//Retrieve the URI string and initialize the App
$uri = getUri();
$host = getHost();
$app = new App($host,$uri);
$uri = $app -> uriPath();

//Initialize the loader class
$load = Loader::instance();

//Load the config class
$config = $load -> core_class('config');

//Load the hooks class
$hooks = $load -> core_class('hooks');

//Load the plugin class
$load -> file('Plugin.php','extend');

//Load the Model, View, and Controller classes class
$load -> file('Model.php','extend');
$load -> file('View.php','extend');
$load -> file('Controller.php','extend');

//Load the router class
$router = $load -> core_class('router');

//Load some config files
$config -> loadConfig('config'); //Loads config.php in global then app dir (if they exist)
$config -> loadConfig('db','database'); //Loads database configuration

//Load the default plugins
$base = Base::instance();
$base -> loadPlugins();

$router -> route($host,$uri);

//$config -> loadConfig('db','database');
echo ' '.(microtime(true) - $start);
?>