<?php



class EndNoteHelperTest extends CDbTestCase
{
    public function testGetCorrectIdentifier()
    {
        $full_doi = "10.5524/101001";
        $identifier = "101001";

        $this->assertEquals($identifier, \EndNoteHelperTest::getRecords($full_doi));

    }

}