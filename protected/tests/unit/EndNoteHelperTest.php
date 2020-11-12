<?php



class EndNoteHelperTest extends CDbTestCase
{
    public function testGetCorrectIdentifier()
    {
        $full_doi = "10.5524/100002";
        $identifier = "100002";

        $this->assertEquals($identifier, \EndNoteHelper::getRecords($full_doi));

    }

}