<?php

namespace tests\unit\models;

use app\models\UpdateGigaDBJob;
use \common\models\Upload;
use app\fixtures\DatasetFixture;
use app\fixtures\AttributeFixture;
use app\fixtures\UnitFixture;
use app\fixtures\SampleFixture;
use \Yii;

class UpdateGigaDBJobTest extends \Codeception\Test\Unit
{
    private $model;
    /**
     * @var \UnitTester
     */
    public $tester;

    /**
     * We need to assume existence of dataset in database hence have fixture for it
     */
    public function _fixtures(){
          return [
            'datasets' => [
                  'class' => DatasetFixture::className(),
                  'dataFile' => codecept_data_dir() . 'dataset.php'
              ],
            'attributes' => [
                  'class' => AttributeFixture::className(),
                  'dataFile' => codecept_data_dir() . 'attribute.php'
              ],
            'units' => [
                  'class' => UnitFixture::className(),
                  'dataFile' => codecept_data_dir() . 'unit.php'
              ],
            'samples' => [
                  'class' => SampleFixture::className(),
                  'dataFile' => codecept_data_dir() . 'sample.php'
              ] 
          ];
    }

    public function _before()
    {
        Yii::$app->db->createCommand("SELECT setval(pg_get_serial_sequence('file', 'id'), coalesce(max(id),0) + 1, false) FROM file;")->execute();
    }

    public function _after()
    {
        // Yii::$app->db->createCommand()->delete('file', 'status = 0')->execute();
    }

    public function testSaveFiles()
    {
        $update = new UpdateGigaDBJob();
        $update->doi = "000007";
        $metadata = [
                'doi' => "000007",
                'name' => "sequence.csv",
                'size' => 24564343,
                'status' => Upload::STATUS_SYNCHRONIZED,
                'location' => "ftp://sequence",
                'extension' => 'CSV',
                'datatype' => 'Sequence assembly'
        ];
        $update->file = $metadata;
        $update->file_attributes = [];
        $update->sample_ids = [];

        $update->saveFile($this->tester->grabRecord('app\models\Dataset',['identifier'=> $metadata['doi']])->id);
        $dbFormat = $this->tester->grabRecord('app\models\FileFormat',['name'=> $metadata['extension']]);
        $dbType = $this->tester->grabRecord('app\models\FileType',['name'=> $metadata['datatype']]);
        $this->tester->seeRecord('app\models\File',[
            'name' => $metadata['name'],
            'size' => $metadata['size'],
            'location' => $metadata['location'],
            'extension' => $metadata['extension'],
            'format_id' => $dbFormat->id,
            'type_id' => $dbType->id,
        ]);
    }

    public function testSaveAttributes()
    {
        $update = new UpdateGigaDBJob();
        $fileId = $this->tester->haveRecord('app\models\File', [
                'dataset_id' => 1,
                'name' => "sequence.csv",
                'location' => "ftp://sequence",
                'extension' => 'CSV',
                'size' => 24564343,
                'format_id' => $this->tester->grabRecord('app\models\FileFormat',['name'=> 'CSV'])->id,
                'type_id' => $this->tester->grabRecord('app\models\FileType',['name'=> 'Sequence assembly'])->id,
        ]);
        $update->file_attributes = [
                        ["name" => "Temperature", "value" => "45", "unit" => "Celsius", "upload_id" => 1],
                        ["name" => "Humidity","value" => "75", "unit" => "%", "upload_id" => 1],
                        ["name" => "Age","value" => "33", "unit" => "Years", "upload_id" => 1],
                    ];
        $result = $update->saveAttributes($fileId);
        foreach ($update->file_attributes as $attr) {
            $attribute = $this->tester->grabRecord('app\models\Attribute',['attribute_name'=> $attr['name']]);
            $unit = $this->tester->grabRecord('app\models\Unit',['name'=> $attr['unit']]);
            $this->tester->seeRecord('app\models\FileAttributes',[
                'file_id' => $fileId,
                'attribute_id' => $attribute->id,
                'value' => $attr['value'],
                'unit_id' => $unit->id,
            ]);        
        }
    }

    public function testSaveSamples()
    {
        $update = new UpdateGigaDBJob();
        $fileId = $this->tester->haveRecord('app\models\File', [
                'dataset_id' => 1,
                'name' => "sequence.csv",
                'location' => "ftp://sequence",
                'extension' => 'CSV',
                'size' => 24564343,
                'format_id' => $this->tester->grabRecord('app\models\FileFormat',['name'=> 'CSV'])->id,
                'type_id' => $this->tester->grabRecord('app\models\FileType',['name'=> 'Sequence assembly'])->id,
        ]);
        $update->sample_ids = ["Sample A", "Sample C"];
        $result = $update->saveSamples($fileId);
        $this->tester->seeRecord('app\models\FileSample',[
                'file_id' => $fileId,
                'sample_id' => 1,
        ]);
        $this->tester->seeRecord('app\models\FileSample',[
                'file_id' => $fileId,
                'sample_id' => 3,
        ]);        
    }

    public function testExecute()
    {
        $mockJob =  $this->createMock(\app\models\UpdateGigaDBJob::class);
        $mockQueue = $this->createMock(\yii\queue\Queue::class);
        $job = new UpdateGigaDBJob();
        $job->self = $mockJob;
        $job->doi = "000007";
        $job->file_attributes = [
                        ["name" => "Temperature", "value" => "45", "unit" => "Celsius", "upload_id" => 1],
                        ["name" => "Humidity","value" => "75", "unit" => "%", "upload_id" => 1],
                        ["name" => "Age","value" => "33", "unit" => "Years", "upload_id" => 1],
                    ];

        $mockJob->expects($this->once())
            ->method('saveFile')
            ->willReturn(1);

        $mockJob->expects($this->once())
            ->method('saveAttributes')
            ->willReturn(true);

        $mockJob->expects($this->once())
            ->method('saveSamples');

        $result = $job->execute($mockQueue);
    }

    public function testThrowExceptionWhenFailedToFindDataset()
    {
        $mockJob =  $this->createMock(\app\models\UpdateGigaDBJob::class);
        $mockQueue = $this->createMock(\yii\queue\Queue::class);
        $job = new UpdateGigaDBJob();
        $job->self = $mockJob;
        $job->doi = "000008";
        $job->file_attributes = [
                        ["name" => "Temperature", "value" => "45", "unit" => "Celsius", "upload_id" => 1],
                        ["name" => "Humidity","value" => "75", "unit" => "%", "upload_id" => 1],
                        ["name" => "Age","value" => "33", "unit" => "Years", "upload_id" => 1],
                    ];

        $mockJob->expects($this->never())
            ->method('saveFile')
            ->willReturn(1);

        $mockJob->expects($this->never())
            ->method('saveAttributes')
            ->willReturn(true);

        $mockJob->expects($this->never())
            ->method('saveSamples');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Dataset record not found for DOI 000008");
        $result = $job->execute($mockQueue);
    }

    public function testThrowExceptionWhenDatasetWrongStatus()
    {
        $mockJob =  $this->createMock(\app\models\UpdateGigaDBJob::class);
        $mockQueue = $this->createMock(\yii\queue\Queue::class);
        $job = new UpdateGigaDBJob();
        $job->self = $mockJob;
        $job->doi = "000009";
        $job->file_attributes = [
                        ["name" => "Temperature", "value" => "45", "unit" => "Celsius", "upload_id" => 1],
                        ["name" => "Humidity","value" => "75", "unit" => "%", "upload_id" => 1],
                        ["name" => "Age","value" => "33", "unit" => "Years", "upload_id" => 1],
                    ];

        $mockJob->expects($this->never())
            ->method('saveFile')
            ->willReturn(1);

        $mockJob->expects($this->never())
            ->method('saveAttributes')
            ->willReturn(true);

        $mockJob->expects($this->never())
            ->method('saveSamples');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Dataset with DOI 000009 has wrong status, Curation needed, got: UserUploading");
        $result = $job->execute($mockQueue);
    }

}
