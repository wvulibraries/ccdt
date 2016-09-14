<?php
/**
 * EngineAPI default private config
 * @package EngineAPI
 */

// This file should be set to be readable only by the web server user and the system administrator (or root)

global $engineVarsPrivate;//MySQL Information

$engineVarsPrivate['engineDB']['driver']        = 'mysql';
$engineVarsPrivate['engineDB']['driverOptions'] = array(
    'host'   => 'localhost',
    'port'   => 3306,
    'user'   => 'username',
    'pass'   => 'password',
    'dbName' => 'EngineAPI'
);

$engineVarsPrivate["privateVars"]["engineDB"] = array(
	array(
		'file'     => 'auth.php',
		'function' => '__construct',
	),
	array(
		'file'     => 'errorHandle.php',
		'function' => 'recordError',
	),
	array(
		'file'     => 'stats.php',
		'function' => '__construct',
	),
	array(
		'file'     => 'mysql.php',
		'function' => 'mysqlLogin',
	),
);
?>
