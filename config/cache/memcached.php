<?php

return [
	/**
	 * Array of memcache servers locations.
	 *   Layout: [
	 *     ['SERVER_HOST', SERVER_PORT, SERVER_WEIGHT],
	 *     ...
	 *   ]
	 */
	'MEMCACHED_HOSTS' => [
		['mc1.localhost' 11211, 50],
		['mc2.localhost', 11211, 50]
	]
];
