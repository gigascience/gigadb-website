<?php

namespace backend\tests;

use backend\models\MoveJob;


/**
 * Test for yii2-queue job class (DTO) for moving files
 * 
 * TODO: very basic for now, just making sure it doesn't crash
 * until I figure out how to test queue job classes
 * 
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 **/
class MoveJobTest extends \Codeception\Test\Unit
{
    /**
     * @var \backend\tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testMoveJob()
    {
        $mockQueue = $this->createMock(\yii\queue\Queue::class);
        $job = new MoveJob();
        $job->execute($mockQueue);
    }

}