<?php
namespace common\tests;

use common\models\Ingest;

class IngestTest extends \Codeception\Test\Unit
{
    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testTimestampBehaviourCreation()
    {
        $ingest = new Ingest();
        $this->assertNotNull($ingest);
        $ingest->save();
        $this->assertNotNull($ingest->created_at);
    }
}