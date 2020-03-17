<?php

namespace common\tests\unit\models;

use Yii;
use common\models\Upload;

/**
 * Login form test
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


}
