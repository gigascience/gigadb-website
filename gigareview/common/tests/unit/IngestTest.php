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
        $this->assertNull($ingest->created_at);
        $this->assertNull($ingest->updated_at);
        $ingest->save();
        $this->assertNotNull($ingest->created_at);
        $ingest->file_name = "dummy.csv";
        sleep(1);
        $ingest->save();
        $this->assertGreaterThan($ingest->created_at, $ingest->updated_at);
    }
}