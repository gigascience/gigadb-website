<?php


class DatasetLogTest extends CDbTestCase
{
    public function testCreateDatasetLogEntry()
    {
        $datasetId = 8;
        $fileName = "Pygoscelis_adeliae.scaf.fa.gz";
        $fileModel = "FileAttribute";
        $modelId = 11020;
        $fileId = "/adminFile/update/id/664";
        $datasetlog = new DatasetLog(); //Instantiate a new Object
        $this->assertNotNull($datasetlog);
        $this->assertTrue(is_a($datasetlog, DatasetLog::class));
        $result = $datasetlog->createDatasetLogEntry($datasetId, $fileName, $fileModel, $modelId, $fileId);
        $this->assertTrue(true===$result, "No new entry in dataset log table");
////        $this->assertEquals($datasetId, $this->dataset_id);
////        $this->assertEquals($fileName, $datasetlog->message);
////        $this->assertEquals($fileModel, $datasetlog->model);
////        $this->assertEquals($modelId, $datasetlog->model_id);
////        $this->assertEquals($fileId, $datasetlog->url);
        $this->assertTrue($datasetlog->isNewRecord);

    }
}