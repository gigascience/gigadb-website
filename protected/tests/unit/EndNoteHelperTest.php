<?php



class EndNoteHelperTest extends CDbTestCase
{

    protected $fixtures=array(
        'dataset'=>'Dataset',
    );

    public function testGetCorrectIdentifier()
    {
        $full_doi = "10.5524/100243";
        $identifier = "100243";

        $this->assertEquals($identifier, \EndNoteHelper::getRecords($full_doi));

    }

}