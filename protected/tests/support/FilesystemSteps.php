<?php

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
/**
 * browser automation steps to setup test users
 *
 * This trait is to be used in functional tests
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
trait FilesystemSteps
{
	/**
	 * Remove filedrop account directories
	 *
	 * to be used with gigadb database
	 *
	 * @param string $doi
	 */
	public function removeDirectories($doi) {
		$adapter = new Local("/home");
		$fs = new Filesystem($adapter);

     	$fs->deleteDir("downloader/$arg1");
     	$fs->deleteDir("uploader/$arg1");
     	$fs->deleteDir("credentials/$arg1");
	}

}
?>