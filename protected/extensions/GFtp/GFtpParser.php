<?php
/* Jison generated parser */

class GFtpParser
{
	public function parse($str) {
		
		if (!preg_match('/^ftp:\/\/[^\/]+$/', $str)){
			throw new GFtpException("connection must start with ftp://");
		}
		$str = substr($str, 6);

		// Split connect string using reverse string
		$parts = explode('@', strrev($str), 2);
		// array("<port>:<url>", "<pass>:<user>") or array("<port>:<url>") 
		
		$res = array();
		
		if (count($parts) >= 1) {
			$hosts = explode(":", $parts[0], 2);
			// array("<port>", "<url>") or array("<url>") 
			if (count($hosts) == 1) {
				$res['host'] = strrev($hosts[0]);
			} else if(count($hosts) == 2) {
				$res['port'] = strrev($hosts[0]);
				$res['host'] = strrev($hosts[1]);
			} else {
				throw new GFtpException("Invalid URL / port");
			}
		}
		if (count($parts) == 2) {
			$hosts = explode(":", strrev($parts[1]), 2);
			// array("<user>", "<pass>") or array("<user>") 
			if (count($hosts) == 1) {
				$res['user'] = $hosts[0];
			} else if(count($hosts) == 2) {
				$res['user'] = $hosts[0];
				$res['pass'] = $hosts[1];
			}
		}

		if (!isset($res['host']))
			throw new GFtpException("No host found");
		
		if (isset($res['port']) && !preg_match('/^[0-9]+/', $res['port'])){
			throw new GFtpException("Port is not a number");
		}
		
		
		Yii::trace(CVarDumper::dumpAsString($res), 'gftp');
		return $res;
	}
}
