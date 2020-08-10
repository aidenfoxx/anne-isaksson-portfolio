<?php

return [
	/**
	 * Display debug information on error.
	 */
	'CORE_DEBUG' => true,

	/**
	 * Cache driver for the application to use.
	 *   Options: 'default', 'apcu', 'memcached', 'redis', 'file'
	 */
	'CORE_CACHE' => 'default',

	/**
	 * Mail driver for the application to use.
	 *   Options: 'default'
	 */
	'CORE_MAILER' => 'default',

	/**
	 * Mail driver for the application to use.
	 *   Options: 'default', 'smarty', 'twig'
	 */
	'CORE_RENDERER' => 'twig',

	/**
	 * Mail driver for the application to use.
	 *   Options: 'default', 'cache'
	 */
	'CORE_SESSION' => 'default',

	/**
	 * Client side session cookie name.
	 */
	'CORE_SESSION_NAME' => 'PHOXX_SESSION'
];
