<?php


class DatasetLogTest extends CDbTestCase
{
    protected $fixtures=array(
        'log'=>'DatasetLog',
    );

    public function testDataSetLogEntryFactory()
    {
        $datasetId = 1;
        $fileName = "Pygoscelis_adeliae.scaf.fa.gz";
        $fileModel = "FileAttributes";
        $modelId = 11020;
        $fileId = 664;
        $datasetlog = DatasetLog::datasetLogEntryFactory($datasetId, $fileName, $fileModel, $modelId, $fileId);
        $this->assertNotNull($datasetlog);
        $this->assertTrue(is_a($datasetlog, DatasetLog::class));
        $this->assertEquals($datasetlog->dataset_id, $this->log(3)->dataset_id);
        $this->assertEquals($datasetlog->message, $this->log(3)->message);
        $this->assertEquals($datasetlog->model, $this->log(3)->model);
        $this->assertEquals($datasetlog->model_id, $this->log(3)->model_id);
        $this->assertEquals($datasetlog->url, $this->log(3)->url);
    }

//    public function testCreateDatasetLogEntry()
//    {
//        $datasetId = 1;
//        $fileName = "Pygoscelis_adeliae.scaf.fa.gz";
//        $fileModel = "FileAttributes";
//        $modelId = 11020;
//        $fileId = 664;
//        $datasetlog = new DatasetLog(); //Instantiate a new Object
//        $result = $datasetlog->createDatasetLogEntry($datasetId, $fileName, $fileModel, $modelId, $fileId);
//        $this->assertTrue(true===$result, "No new entry in dataset log table");
//        $this->assertTrue($datasetlog->isNewRecord);
//    }
}