<?php


class DatasetLogTest extends CDbTestCase
{
    public function testCreateDatasetLogEntry()
    {
        $datasetId = 8;
        $fileName = "Pygoscelis_adeliae.scaf.fa.gz: file attribute deleted";
        $fileModel = "FileAttribute";
        $modelId = 11020;
        $fileId = "/adminFile/update/id/664";
        $datasetlog = new DatasetLog(); //Instantiate a new Object
        $datasetlog->createDatasetLogEntry($datasetId, $fileName, $fileModel, $modelId, $fileId);
        $this->assertNotNull($datasetlog);
        $this->assertTrue(is_a($datasetlog, DatasetLog::class));
//        $this->assertEquals($datasetId, $this->dataset_id);
//        $this->assertEquals($fileName, $datasetlog->message);
//        $this->assertEquals($fileModel, $datasetlog->model);
//        $this->assertEquals($modelId, $datasetlog->model_id);
//        $this->assertEquals($fileId, $datasetlog->url);
        $this->assertTrue($datasetlog->isNewRecord);
    }
}