<?php

declare(strict_types=1);

class ManuscriptTest extends CDbTestCase
{

    protected $fixtures = ['manuscripts' => 'Manuscript'];

    public function testSaveValidManuscript()
    {
        $model = new \GigaDB\models\Manuscript();
        $model->id = 9;
        $model->identifier = 'test_identifier_123';
        $model->pmid = 123456;
        $model->dataset_id = 1;
        $model->is_pre_print = true;

        $this->assertTrue($model->validate(), 'Error');
        $this->assertTrue($model->save(), 'Error');

        $this->assertNotNull($model->id);
        $this->assertTrue($model->is_pre_print);
    }

    public function testValidationFailsWithoutPmidAsInteger()
    {
        $model = new \GigaDB\models\Manuscript();
        $model->identifier = 'test_identifier_123';
        $model->pmid = 'abc';
        $model->dataset_id = 1;
        $model->is_pre_print = true;

        $this->assertFalse($model->validate(), 'Error');
        $this->assertArrayHasKey('pmid', $model->getErrors(), 'Error on pmid');
    }

    public function testValidationFailsWithoutIdentifier()
    {
        $model = new \GigaDB\models\Manuscript();
        $model->pmid = 123456;
        $model->dataset_id = 1;

        $this->assertFalse($model->validate(), 'Error');
        $this->assertArrayHasKey('identifier', $model->getErrors(), 'Error on pmid');
    }

    public function testValidationFailsWithoutDatasetId()
    {
        $model = new \GigaDB\models\Manuscript();
        $model->identifier = 'test_identifier_123';
        $model->pmid = 123456;

        $this->assertFalse($model->validate(), 'Error');
        $this->assertArrayHasKey('dataset_id', $model->getErrors(), 'Error on pmid');
    }

    public function testValidationFailsWithIdentifierOver32character()
    {
        $model = new \GigaDB\models\Manuscript();
        $model->identifier = 'azertyuiopmlkjhgfdsqwxcvbnazertyui';
        $model->pmid = 123456;
        $model->dataset_id = 1;

        $this->assertFalse($model->validate(), 'Error');
        $this->assertArrayHasKey('identifier', $model->getErrors(), 'Error on pmid');
    }

    public function testSetPrePrintAsFalseByDefault()
    {
        $model = new \GigaDB\models\Manuscript();
        $model->identifier = 'test_identifier_123';
        $model->pmid = 123456;
        $model->dataset_id = 1;
        $model->id = 9;

        $this->assertTrue($model->validate(), 'Error');
        $this->assertTrue($model->save(), 'Error');

        $this->assertFalse($model->is_pre_print);
    }
}
