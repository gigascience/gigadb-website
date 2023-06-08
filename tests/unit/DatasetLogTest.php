<?php

class DatasetLogTest extends CDbTestCase
{
    public function testDataSetLogEntryFactory()
    {
        // With reference to the entry in dataset_log fixture
        $datasetId = 1;
        $fileName = "File Tinamus_guttatus.fa.gz";
        $fileModel = "File";
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

    // Includde Dataset fixture to avoid unique duplicate key value violation with dataset_log_pkey
    protected $fixtures = array(
        'datasets' => 'Dataset',
    );

    public function testCreateDatasetLogEntry()
    {
        $datasetId = 1;
        $fileName = "File Tinamus_guttatus.fa.gz";
        $fileModel = "File";
        $modelId = 16945;
        $fileId = 16945; //mockup ID
        $saveNewEntry = DatasetLog::createDatasetLogEntry($datasetId, $fileName, $fileModel, $modelId, $fileId);
        $this->assertTrue(is_bool($saveNewEntry) === true, "bool is returned");
        $this->assertTrue(true === $saveNewEntry, "No new entry is saved to dataset log table");

        // To assert the delete message will be generated as expected
        $datasetlog = DatasetLog::model()->findByPk($datasetId);
        $this->assertEquals("File Tinamus_guttatus.fa.gz: file attribute deleted", $datasetlog->message, "Delete message was generated in different format");
    }
}
