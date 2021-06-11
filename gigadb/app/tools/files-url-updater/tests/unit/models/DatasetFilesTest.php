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
        $this->tester->haveInDatabase('public.dataset',[
            'id'=>2,
            'submitter_id'=>345,
            'identifier'=>"100683",
            'title'=>'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
            'description'=>'The emergence of Zika virus (ZIKV) as a public health emergency in Latin America and the Caribbean (LAC) occurred during a period of severe drought and unusually high temperatures. Speculation in the literature exists that these climate conditions were associated with the 2015/2016 El Niño event and/or climate change but to date no quantitative ',
            'dataset_size'=>1073741824,
            'ftp_site'=>'ftp://climb.genomics.cn/pub/10.5524/100001_101000/100683',
            'upload_status'=>'Published',
            'publication_date'=>'2018-08-23',
            'publisher_id'=>1,
        ],);
        $this->tester->haveInDatabase('public.dataset',[
            'id'=>3,
            'submitter_id'=>345,
            'identifier'=>"100373",
            'title'=>'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
            'description'=>'The emergence of Zika virus (ZIKV) as a public health emergency in Latin America and the Caribbean (LAC) occurred during a period of severe drought and unusually high temperatures. Speculation in the literature exists that these climate conditions were associated with the 2015/2016 El Niño event and/or climate change but to date no quantitative ',
            'dataset_size'=>1073741824,
            'ftp_site'=>'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100373/',
            'upload_status'=>'Published',
            'publication_date'=>'2018-08-23',
            'publisher_id'=>1,
        ],);
        $this->tester->haveInDatabase('public.dataset',[
            'id'=>4,
            'submitter_id'=>345,
            'identifier'=>"000007",
            'title'=>'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
            'description'=>'The emergence of Zika virus (ZIKV) as a public health emergency in Latin America and the Caribbean (LAC) occurred during a period of severe drought and unusually high temperatures. Speculation in the literature exists that these climate conditions were associated with the 2015/2016 El Niño event and/or climate change but to date no quantitative ',
            'dataset_size'=>1073741824,
            'ftp_site'=>'ftp://parrot.genomics.cn/gigadb/pub/10.5524/000001_101000/000007/',
            'upload_status'=>'Published',
            'publication_date'=>'2018-08-23',
            'publisher_id'=>1,
        ],);
        $this->tester->haveInDatabase('public.dataset',[
            'id'=>5,
            'submitter_id'=>345,
            'identifier'=>"100883",
            'title'=>'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
            'description'=>'The emergence of Zika virus (ZIKV) as a public health emergency in Latin America and the Caribbean (LAC) occurred during a period of severe drought and unusually high temperatures. Speculation in the literature exists that these climate conditions were associated with the 2015/2016 El Niño event and/or climate change but to date no quantitative ',
            'dataset_size'=>1073741824,
            'ftp_site'=>'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100883/',
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
        $this->tester->haveInDatabase('public.file',[
            'id' => 2,
            'dataset_id' => 2,
            'name' => "readme_100683.txt",
            'location'=>'ftp://climb.genomics.cn/pub/10.5524/100001_101000/100683/100683/readme_100683.txt',
            'extension'=>'txt',
            'size'=>'1322123045',
            'description'=>'just readme',
            'date_stamp' => '2015-10-12',
            'format_id' => 1,
            'type_id' => 1,
            'download_count'=>0,
        ],);
        $this->tester->haveInDatabase('public.file',[
            'id' => 3,
            'dataset_id' => 2,
            'name' => "HAMAP-SPARQL-master.zip",
            'location'=>'ftp://climb.genomics.cn/pub/10.5524/100001_101000/100683/100683/HAMAP-SPARQL-master.zip',
            'extension'=>'txt',
            'size'=>'1322123045',
            'description'=>'just readme',
            'date_stamp' => '2015-10-12',
            'format_id' => 1,
            'type_id' => 1,
            'download_count'=>0,
        ],);
        $this->tester->haveInDatabase('public.file',[
            'id' => 4,
            'dataset_id' => 3,
            'name' => "NA12878_1_fq.tar.gz",
            'location'=>'ftp://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100373/fastq/NA12878_1_fq.tar.gz',
            'extension'=>'txt',
            'size'=>'1322123045',
            'description'=>'just readme',
            'date_stamp' => '2015-10-12',
            'format_id' => 1,
            'type_id' => 1,
            'download_count'=>0,
        ],);
        $this->tester->haveInDatabase('public.file',[
            'id' => 5,
            'dataset_id' => 3,
            'name' => "bb_snp.vcf.gz",
            'location'=>'ftp://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100373/vcf/bb_snp.vcf.gz',
            'extension'=>'txt',
            'size'=>'1322123045',
            'description'=>'just readme',
            'date_stamp' => '2015-10-12',
            'format_id' => 1,
            'type_id' => 1,
            'download_count'=>0,
        ],);
        $this->tester->haveInDatabase('public.file',[
            'id' => 6,
            'dataset_id' => 3,
            'name' => "readme.txt",
            'location'=>'ftp://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100373/readme.txt',
            'extension'=>'txt',
            'size'=>'1322123045',
            'description'=>'just readme',
            'date_stamp' => '2015-10-12',
            'format_id' => 1,
            'type_id' => 1,
            'download_count'=>0,
        ],);
        $this->tester->haveInDatabase('public.file',[
            'id' => 7,
            'dataset_id' => 5,
            'name' => "readme_100883.txt",
            'location'=>'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100883/readme_100883.txt',
            'extension'=>'txt',
            'size'=>'1322123045',
            'description'=>'just readme',
            'date_stamp' => '2015-10-12',
            'format_id' => 1,
            'type_id' => 1,
            'download_count'=>0,
        ],);
        $this->tester->haveInDatabase('public.file',[
            'id' => 8,
            'dataset_id' => 5,
            'name' => "3_babesia_classification-test-RBCell",
            'location'=>'https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100883/3_babesia_classification/test/RBCell',
            'extension'=>'txt',
            'size'=>'1322123045',
            'description'=>'just readme',
            'date_stamp' => '2015-10-12',
            'format_id' => 1,
            'type_id' => 1,
            'download_count'=>0,
        ],);
        $this->tester->haveInDatabase('public.file',[
            'id' => 9,
            'dataset_id' => 5,
            'name' => "3_babesia_classification-train-babesia_ours",
            'location'=>'https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100883/3_babesia_classification/train/babesia_ours',
            'extension'=>'txt',
            'size'=>'1322123045',
            'description'=>'just readme',
            'date_stamp' => '2015-10-12',
            'format_id' => 1,
            'type_id' => 1,
            'download_count'=>0,
        ],);
        $this->tester->haveInDatabase('public.file',[
            'id' => 10,
            'dataset_id' => 5,
            'name' => "Babesia_Parasite_Recognition_file_metadata.csv",
            'location'=>'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100883/Babesia_Parasite_Recognition_file_metadata.csv',
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
        $this->assertEquals(4, count(DatasetFiles::build()->getAllPendingDatasets()));
        $this->assertEquals(2, count(DatasetFiles::build()->getNextPendingDatasets(0, 2)) );
        $this->assertEquals(4, count(DatasetFiles::build()->getNextPendingDatasets(0, 5)) );
        $this->assertEquals(0, count(DatasetFiles::build()->getNextPendingDatasets(0, 0)) );
        $this->assertEquals(1, count(DatasetFiles::build()->getNextPendingDatasets(0, 1)) );
        $this->assertEquals(1, count(DatasetFiles::build()->getNextPendingDatasets(4, 10)) );
        $this->assertEquals(3, count(DatasetFiles::build()->getNextPendingDatasets(1, 10)) );
    }
}