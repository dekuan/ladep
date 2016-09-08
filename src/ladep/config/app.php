<?php

return
[
	'debug'				=> true,
	'langcode'			=> 'usa',

	//
	//	local directory
	//
	'dir_release'			=> 'releases',
	'dir_wwwroot'			=> 'wwwroot',
	'dir_projects'			=> 'project',
	'dir_logs'			=> 'logs',

	//	supported extensions
	'ext_project'			=> [ 'ladep', 'xsdep' ],


	//
	//	laravel directory
	//
	'dir_la_resources_views'	=> '/resources/views/',
	'dir_la_public'			=> '/public/',


	//
	//	split char
	//
	'split_char_of_version'		=> '-_-',

	//
	//	timeout of executing a command
	//
	'cmd_timeout'			=> 60 * 30,

	//
	//	status file
	//
	'path_file_status'		=> '.ladepstatus',

	//
	//	.env file
	//
	'path_file_env'			=> '.env',

	//	/config/app.php
	'path_file_config_app'		=> 'app.php',

	//	/config/app.php
	'path_file_config_database'	=> 'database.php',


	//	files to be deleted before composer install
	'path_cleanup_full_before_composer_install'	=>
		[
			'/vendor/'		=> true,		//	true  : remove root dir
			'composer.lock'		=> false,
		],

	//	files to be deleted
	'path_cleanup_full_after_composer_install'		=>
		[
			'/database/migrations/'		=> false,	//	false : do not remove root dir
			'/public/logs/'			=> true,	//	true  : remove root dir
		//	'/storage/app/'			=> false,
		//	'/storage/cache/'		=> false,
		//	'/storage/sessions/'		=> false,
		//	'/storage/views/'		=> false,
		//	'/storage/logs/'		=> false,
			'/storage/framework/cache/'	=> false,
			'/storage/framework/sessions/'	=> false,
			'/storage/framework/views/'	=> false,
			'/tests/'			=> true,
			'/.idea/'			=> true,
			'/.git/'			=> true,
			'.DS_Store'			=> false,
			'.gitignore'			=> false,
			'.gitattributes'		=> false,
			'.env.example'			=> false,
			'readme.md'			=> false,
			'README.MD'			=> false,
		],

	//	all of matched dir that named as below
	'path_cleanup_matched_dirs'	=>
		[
			'.git'				=> false,
			'.idea'				=> false,
		],

	//	all of matched file that named as below
	'path_cleanup_matched_files'	=>
		[
			'.gitignore'			=> false,	//	false : do not remove root dir
			'.gitkeep'			=> false,	//	true  : remove root dir
			'.DS_Store'			=> false,
			'LICENSE'			=> false,
			'LICENSE.txt'			=> false,
			'readme.md'			=> false,
			'CONTRIBUTING.md'		=> false,
			'composer.lock'			=> false,
		],

	//
	//	default pages for http error
	//
	'path_http_error_files'		=>
		[
			'/resources/errors/404.blade.php'	=> '/resources/views/errors/404.blade.php',
			'/resources/errors/500.blade.php'	=> '/resources/views/errors/500.blade.php',
			'/resources/errors/502.blade.php'	=> '/resources/views/errors/502.blade.php',
			'/resources/errors/503.blade.php'	=> '/resources/views/errors/503.blade.php',
		],

	//	...
	'path_chmod_dirs'		=>
		[
			'/storage/'			=> 750,		//	-rwxr-x---
			'/bootstrap/cache/'		=> 750,		//	-rwxr-x---
			'/vendor/'			=> 750,		//	-rwxr-x---
			'/public/'			=> 750,		//	-rwxr-x---
			'/resources/'			=> 750,		//	-rwxr-x---
			'/resources/views/errors/'	=> 750,		//	-rwxr-x---
		],
];