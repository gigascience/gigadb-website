<?php

namespace common\tests\Helper;

/**
 * Before/After test hooks for the Filesystem module goes here.
 *
 * Don't put any kind of before/after test hooks here.
 * Instead, subclass the module for which you want to create hooks
 * like here.
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FilesystemCleanupHook extends \Codeception\Module\Filesystem
{

    // HOOK: after each test scenario
    public function _after(\Codeception\TestInterface $test)
    {
    	$doi = "100007";
    	$this->deleteDir("/var/incoming/ftp/$doi");
    	$this->deleteDir("/var/incoming/credentials/$doi");
    	$this->deleteDir("/var/repo/$doi");
    }
}

 ?>