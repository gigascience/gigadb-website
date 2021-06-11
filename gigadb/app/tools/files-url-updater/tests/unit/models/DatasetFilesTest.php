<?php

namespace tests\unit\models;

use app\models\DatasetFiles;

class DatasetFilesTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    public $tester;

    public function _before()
    {
        parent::_before();

        $this->tester->haveInDatabase('public.gigadb_user',[
            'id' => '345',
            'email' => 'user@gigadb.org',
            'password' => '5a4f75053077a32e681f81daa8792f95',
            'first_name' => 'John',
            'last_name' => 'Smith',
            'affiliation' => 'BGI',
            'role' => 'user',
            'is_activated' => true,
            'newsletter' => false,
            'previous_newsletter_state' => true,
            'username' => 'user@gigadb.org',
            'preferred_link' => 'EBI',
        ],);
        $this->tester->haveInDatabase('public.publisher', [
            'id' => 1,
            'name'=>'Gigascience',
            'description'=>'',
        ],);
        $this->tester->haveInDatabase('public.file_format', [
            'id' => 1,
            'name'=>'TEXT',
            'description'=>' (.doc, .readme, .text, .txt) - a text file',
        ],);
        $this->tester->haveInDatabase('public.file_type', [
            'id' => 1,
            'name'=>'Text',
            'description'=>'This is starting guide',
        ],);
        $this->tester->haveInDatabase('public.dataset',[
            'id'=>1,
            'submitter_id'=>345,
            'identifier'=>"100243",
            'title'=>'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
            'description'=>'The emergence of Zika virus (ZIKV) as a public health emergency in Latin America and the Caribbean (LAC) occurred during a period of severe drought and unusually high temperatures. Speculation in the literature exists that these climate conditions were associated with the 2015/2016 El Niño event and/or climate change but to date no quantitative ',
            'dataset_size'=>1073741824,
            'ftp_site'=>'ftp://parrot.genomics.cn/pub/10.5524/100001_101000/100243/',
            'upload_status'=>'Published',
            'publication_date'=>'2018-08-23',
            'publisher_id'=>1,
        ],);
        $this->tester->haveInDatabase('public.file',[
            'id' => 1,
            'dataset_id' => 1,
            'name' => "readme2.txt",
            'location'=>'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100243/readme.txt',
            'extension'=>'txt',
            'size'=>'1322123045',
            'description'=>'just readme',
            'date_stamp' => '2015-10-12',
            'format_id' => 1,
            'type_id' => 1,
            'download_count'=>0,
        ],);

    }

    public function testListPendingDatasets() {

//        $I->seeInDatabase('file',['location like' => 'genomics.cn']);
        $this->tester->seeInDatabase('public.gigadb_user',['last_name' => 'Smith']);
        $this->assertTrue(true);
        //$this->assertEquals(4, count(DatasetFiles::build()->getAllPendingDatasets()));
    }
}