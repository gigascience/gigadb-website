<?php

declare(strict_types=1);

require_once __DIR__ . '/LoadingFixtureTrait.php';

use Codeception\Test\Unit;

class DatasetLogTest extends Unit
{
    use LoadingFixtureTrait;

    public function _before()
    {
        $db = $this->getModule('Db')->_getDbh();
        $db->exec('TRUNCATE TABLE gigadb_user CASCADE');
        $db->exec('TRUNCATE TABLE dataset CASCADE');

        $this->loadFixture('gigadb_user', \User::class);
        $this->loadFixture('dataset', \Dataset::class);
    }

    public function testDataSetLogEntryFactory()
    {
        // With reference to the entry in dataset_log fixture
        $datasetId = 1;
        $fileName = 'File Tinamus_guttatus.fa.gz';
        $fileModel = 'File';
        $modelId = 16945;
        $fileId = 16945; //mockup ID
        $datasetlog = DatasetLog::makeNewInstanceForDatasetLogBy($datasetId, $fileName, $fileModel, $modelId, $fileId);
        $this->assertNotNull($datasetlog);
        $this->assertTrue(is_a($datasetlog, DatasetLog::class));
        $this->assertEquals($datasetId, $datasetlog->dataset_id);
        $this->assertEquals($fileName, $datasetlog->message);
        $this->assertEquals($fileModel, $datasetlog->model);
        $this->assertEquals($modelId, $datasetlog->model_id);
        $this->assertEquals("./vendor/codeception/codeception/adminFile/update/id/$fileId", $datasetlog->url);
        $this->assertTrue($datasetlog->isNewRecord);
    }

    public function testCreateDatasetLogEntry()
    {
        $datasetId = 1;
        $fileName = 'File Tinamus_guttatus.fa.gz';
        $fileModel = 'File';
        $modelId = 16945;
        $fileId = 16945; //mockup ID
        $saveNewEntry = DatasetLog::createDatasetLogEntry($datasetId, $fileName, $fileModel, $modelId, $fileId);
        $this->assertTrue(is_bool($saveNewEntry) === true, "bool is returned");
        $this->assertTrue(true === $saveNewEntry, "No new entry is saved to dataset log table");

        // To assert the delete message will be generated as expected
        $datasetlog = DatasetLog::model()->findAllByAttributes(["dataset_id" => $datasetId]);
        $this->assertEquals("File Tinamus_guttatus.fa.gz: file attribute deleted", $datasetlog[1]->message, "Delete message was generated in different format");
    }


    public function testSaveValidDatasetLog()
    {
        $model = new DatasetLog();
        $model->dataset_id = 1;
        $model->message = 'Test message';

        $this->assertTrue($model->validate(), 'Error');
        $this->assertTrue($model->save(), 'Error');

        $this->assertNotNull($model->id);
    }

    public function testItFailsWithoutMessage()
    {
        $model = new DatasetLog();
        $model->dataset_id = 1;
        $this->assertFalse($model->validate(), 'Error');
    }

    public function testItFailsWithoutDatasetId()
    {
        $model = new DatasetLog();
        $model->message = 'Test message';

        $this->assertFalse($model->validate(), 'Error');
    }
}
