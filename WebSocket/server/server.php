<?php
/* This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. */

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);

// require(__DIR__ . '/lib/SplClassLoader.php');

// $classLoader = new SplClassLoader('WebSocket', __DIR__ . '/lib');
// $classLoader->register();

$pathMyBelote = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'MyBelote'.DIRECTORY_SEPARATOR;

require_once $pathMyBelote. 'Autoloader.php';

//definition des namespaces
$config = array("ns"=>array(
		'WebSocket'=> __DIR__ . DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR,
		"Belote"=>$pathMyBelote,
		"STANDARD"=>$pathMyBelote.'Belote'.DIRECTORY_SEPARATOR
));
\Autoloader::register($config);

//$server = new \WebSocket\Server('10.184.49.142', 8000, false);
$server = new \WebSocket\Server('127.0.0.1', 8000, false);

// $server = new \WebSocket\Server('192.168.0.13', 8000, false);


// server settings:
$server->setMaxClients(100);
$server->setCheckOrigin(true);
$server->setAllowedOrigin('10.184.49.142:1180');
$server->setMaxConnectionsPerIp(100);
$server->setMaxRequestsPerMinute(2000);

// Hint: Status application should not be removed as it displays usefull server informations:
$server->registerApplication('status', \WebSocket\Application\StatusApplication::getInstance());
// $server->registerApplication('demo', \WebSocket\Application\DemoApplication::getInstance());
$server->registerApplication('belote', \WebSocket\Application\BeloteApplication::getInstance());

$server->run();