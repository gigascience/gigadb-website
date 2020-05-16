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

    // HOOK: after failure
    // public function _failed(\Codeception\TestInterface $test, $fail)
    // {
    // 	$this->writeToFile("/var/tmp/processing_flag/failure", "fail");
    // }

    // HOOK: after each test scenario
    public function _after(\Codeception\TestInterface $test)
    {
    	$dois = ["000007","100005","100006"];
    	foreach ($dois as $doi) {
	    	$this->deleteDir("/var/incoming/ftp/$doi");
	    	$this->deleteDir("/var/private/$doi");
	    	$this->deleteDir("/var/repo/$doi");
            $this->deleteDir("/var/ftp/public/$doi");
	    	$this->deleteDir("/var/tmp/processing_flag/$doi");
    	}
        // clear out the message queue
        exec("./yii monitor/clear-all --interactive=0");
    }
}

 ?>