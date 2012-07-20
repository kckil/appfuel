<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Startup;

/**
 * Look for the server name and set the base url constant
 */
class UrlTask extends StartupTask 
{
	/**
	 * @return	bool
	 */
	public function execute()
	{
		if (! isset($_SERVER['HTTP_HOST'])) {
			return;
		}

		$scheme = isset($_SERVER['HTTPS']) ? 'https': 'http';
		$host   = $_SERVER['HTTP_HOST'];
		$url    = "{$scheme}://$host";
		if (! defined('AF_BASE_URL')) {
			define('AF_BASE_URL', $url);
		}
	}
}
