<?php

namespace tests\unit\models;

use app\models\UpdateGigaDBJob;

class UpdateGigaDBJobTest extends \Codeception\Test\Unit
{
    private $model;
    /**
     * @var \UnitTester
     */
    public $tester;

    public function testCreateFiles()
    {
        $mockGigaDBQueue = $this->createMock(\yii\queue\Queue::class);
        $update = new UpdateGigaDBJob();

        $result = $update->execute($mockGigaDBQueue);
        $this->assertTrue($result);
    }
}
