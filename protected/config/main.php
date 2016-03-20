<?php

use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\ReadPreference;

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
Yii::setPathOfAlias('mongoyii', dirname(dirname(__FILE__)) . '/extensions/MongoYii');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Wikiera',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',

	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'Enter Your Password Here',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		*/
	),

	// application components
	'components'=>array(

		'session' => array(
			'class' => 'mongoyii\util\Session',
		),


		'cache' => array(
			'class' => 'mongoyii\util\Cache',
		),

		'user'=>array(
			'class' => 'EWebUser',
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		
		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				//'<controller:\w+>/<id:.{24,}>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:.{24,}>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
		'mongodb' => array(
			'class' => 'mongoyii\Client',
			'uri' => 'mongodb://sam:blah@localhost:27017/admin',
			'options' => [],
			'driverOptions' => [],
			'db' => [
				'super_test' => [
					'writeConcern' => new WriteConcern(1),
					'readPreference' => new ReadPreference(ReadPreference::RP_PRIMARY),
				]
			],
			'enableProfiling' => true
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	//'defaultController' => 'article',

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);