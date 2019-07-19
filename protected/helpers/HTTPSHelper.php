<?php

const BLACK_LIST = [
	"penguin.genomics.cn",
];

/**
 * replace urls containing hardcoded "http://" with "https://"
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class HTTPSHelper
{
	/**
	 * convert a string containing hard-coded "http://" to "https://" and return it
	 *
	 * @param string $url
	 * @return string|false converted url or false if problem or url is blacklisted
	 **/
	public static function httpsize(string $url)
	{
		$url_string = trim($url);
		$uri = parse_url($url_string);
		if (isset($uri['host']) && !in_array($uri['host'], BLACK_LIST) ) {
			return preg_replace('/http:/', 'https:', $url_string);
		}
		return false;
	}
}
?>