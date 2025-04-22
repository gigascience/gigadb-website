<?php

declare(strict_types=1);

class DatasetLogTest extends CDbTestCase
{
    protected $fixtures = ['datasets' => 'Dataset'];

    public function testDataSetLogEntryFactory()
    {
        // With reference to the entry in dataset_log fixture
        $datasetId = 1;
        $fileName = 'File Tinamus_guttatus.fa.gz';
        $fileModel = 'File';
        $modelId = 16945;
        $fileId = 16945; //mockup ID
        $datasetlog = \GigaDB\models\DatasetLog::makeNewInstanceForDatasetLogBy($datasetId, $fileName, $fileModel, $modelId, $fileId);
        $this->assertNotNull($datasetlog);
        $this->assertTrue(is_a($datasetlog, \GigaDB\models\DatasetLog::class));
        $this->assertEquals($datasetId, $datasetlog->dataset_id);
        $this->assertEquals($fileName, $datasetlog->message);
        $this->assertEquals($fileModel, $datasetlog->model);
        $this->assertEquals($modelId, $datasetlog->model_id);
        $this->assertEquals("./vendor/codeception/codeception/codecept?r=admin-file%2Fupdate&id=$fileId", $datasetlog->url);
        $this->assertTrue($datasetlog->isNewRecord);
    }

    public function testCreateDatasetLogEntry()
    {
        $datasetId = 1;
        $fileName = 'File Tinamus_guttatus.fa.gz';
        $fileModel = 'File';
        $modelId = 16945;
        $fileId = 16945; //mockup ID
        $saveNewEntry = \GigaDB\models\DatasetLog::createDatasetLogEntry($datasetId, $fileName, $fileModel, $modelId, $fileId);
        $this->assertTrue(is_bool($saveNewEntry) === true, 'bool is returned');
        $this->assertTrue(true === $saveNewEntry, 'No new entry is saved to dataset log table');

        // To assert the delete message will be generated as expected
        $datasetlog = \GigaDB\models\DatasetLog::findOne(['dataset_id' => $datasetId]);
        $this->assertEquals('File Tinamus_guttatus.fa.gz: file attribute deleted', $datasetlog->message, 'Delete message was generated in different format');
    }

    public function testSaveValidDatasetLog()
    {
        $model = new \GigaDB\models\DatasetLog();
        $model->dataset_id = 1;
        $model->message = 'Test message';

        $this->assertTrue($model->validate(), 'Error');
        $this->assertTrue($model->save(), 'Error');

        $this->assertNotNull($model->id);
    }

    public function testItFailsWithoutMessage()
    {
        $model = new \GigaDB\models\DatasetLog();
        $model->dataset_id = 1;

        $this->assertFalse($model->validate(), 'Error');
    }

    public function testItFailsWithoutDatasetId()
    {
        $model = new \GigaDB\models\DatasetLog();
        $model->message = 'Test message';

        $this->assertFalse($model->validate(), 'Error');
    }
}
