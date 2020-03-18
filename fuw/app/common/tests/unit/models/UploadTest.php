<?php

namespace common\tests\unit\models;

use Yii;
use common\models\Upload;

/**
 * Test validations in Upload model.
 */
class UploadTest extends \Codeception\Test\Unit
{
    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;

    public function testValidateDataTypeError()
    {
        $model = new Upload([
                'doi' => "000000",
                'name' => "sequence.csv",
                'size' => 24564343,
                'status' => Upload::STATUS_UPLOADING,
                'location' => "ftp://sequence",
                'extension' => 'CSV',
                'datatype' => 'Monologue'
          ]);

        $model->validate();
        expect('Error message is raised for datatype attribute', $model->errors)->hasKey('datatype');
        $this->assertEquals('Data type is not recognized: Monologue', $model->errors['datatype'][0]);
    }

    public function testValidateDataTypeNoError()
    {
        $model = new Upload([
                'doi' => "000000",
                'name' => "sequence.csv",
                'size' => 24564343,
                'status' => Upload::STATUS_UPLOADING,
                'location' => "ftp://sequence",
                'extension' => 'CSV',
                'datatype' => 'Annotation'
          ]);

        $model->validate();
        expect('error message should not be set', $model->errors)->hasntKey('datatype');
    }

    public function testValidateFileFormatError()
    {
        $model = new Upload([
                'doi' => "000000",
                'name' => "sequence.csv",
                'size' => 24564343,
                'status' => Upload::STATUS_UPLOADING,
                'location' => "ftp://sequence",
                'extension' => 'ZZZ',
                'datatype' => 'Annotation'
          ]);

        $model->validate();
        expect('Error message is raised for extension attribute', $model->errors)->hasKey('extension');
        $this->assertEquals('File format is not recognized: ZZZ', $model->errors['extension'][0]);
    }

    public function testValidateFileFormatNoError()
    {
        $model = new Upload([
                'doi' => "000000",
                'name' => "sequence.csv",
                'size' => 24564343,
                'status' => Upload::STATUS_UPLOADING,
                'location' => "ftp://sequence",
                'extension' => 'CSV',
                'datatype' => 'Annotation'
          ]);

        $model->validate();
        expect('error message should not be set', $model->errors)->hasntKey('extension');
    }


}
