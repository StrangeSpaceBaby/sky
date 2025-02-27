<?php

return
[
	'paths' => [
		'migrations' => '%%PHINX_CONFIG_DIR%%/conf/db/migrations',
		'seeds' => '%%PHINX_CONFIG_DIR%%/conf/db/seeds'
	],
	'environments' => [
		'default_migration_table' => '_migration',
		'default_environment' => 'development',
		'production' => [
			'adapter' => 'mysql',
			'host' => 'localhost',
			'name' => 'production_db',
			'user' => 'root',
			'pass' => '',
			'port' => '3306',
			'charset' => 'utf8',
			'collation' => 'utf8mb4_unicode_520_nopad_ci'
		],
		'development' => [
			'adapter' => 'mysql',
			'host' => 'localhost',
			'name' => 'andbam',
			'user' => 'root',
			'pass' => 'root',
			'port' => '3306',
			'charset' => 'utf8',
			'collation' => 'utf8mb4_unicode_520_nopad_ci'
		],
		'testing' => [
			'adapter' => 'mysql',
			'host' => 'localhost',
			'name' => 'testing_db',
			'user' => 'root',
			'pass' => '',
			'port' => '3306',
			'charset' => 'utf8',
			'collation' => 'utf8mb4_unicode_520_nopad_ci'
		]
	],
	'version_order' => 'creation',
	'templates' => [
		'file' => '%%PHINX_CONFIG_DIR%%/conf/db/migration_template.tpl',
		'style' => 'up_down',
		'seedFile' => '%%PHINX_CONFIG_DIR%%/conf/db/seeder_template.tpl'
	],
	'data_domain' => [
		'json' => [
			'type' => 'json',
		]
	],
];
