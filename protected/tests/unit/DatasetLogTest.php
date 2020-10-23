<?php


class DatasetLogTest extends CDbTestCase
{
    public function testDatasetLogEntryFactory()
    {
        $datasetId = 8;
        $fileName = "Pygoscelis_adeliae.scaf.fa.gz";
        $fileModel = "FileAttributes";
        $modelId = 11020;
        $fileId = 664;
        $datasetlog = DatasetLog::datasetLogEntryFactory($datasetId, $fileName, $fileModel, $modelId, $fileId);
        $this->assertNotNull($datasetlog);
        $this->assertTrue(is_a($datasetlog, DatasetLog::class));
        $this->assertEquals($datasetId, $datasetlog->dataset_id);
        $this->assertEquals("$fileName: file attribute deleted", $datasetlog->message);
        $this->assertEquals($fileModel, $datasetlog->model);
        $this->assertEquals($modelId, $datasetlog->model_id);
        $this->assertEquals("./bin/adminFile/update/id/$fileId", $datasetlog->url);
    }

    public function testCreateDatasetLogEntry()
    {
        $datasetId = 8;
        $fileName = "Pygoscelis_adeliae.scaf.fa.gz";
        $fileModel = "FileAttributes";
        $modelId = 11020;
        $fileId = 664;
        $datasetlog = new DatasetLog(); //Instantiate a new Object
        $result = $datasetlog->createDatasetLogEntry($datasetId, $fileName, $fileModel, $modelId, $fileId);
        $this->assertTrue(true===$result, "No new entry in dataset log table");
        $this->assertTrue($datasetlog->isNewRecord);
    }
}