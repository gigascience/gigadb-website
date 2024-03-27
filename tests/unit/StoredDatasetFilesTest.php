<?php

/**
 * Unit tests for StoredDatasetFiles to retrieve from storage, the files for associated dataset
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetFilesTest extends CDbTestCase
{
    protected $fixtures = array( //careful, the order matters here because of foreign key constraints
        'species' => 'Species',
        'datasets' => 'Dataset',
        'attributes' => 'Attributes',
        'file_formats' => 'FileFormat',
        'file_types' => 'FileType',
        'files' => 'File',
        'file_attributes' => 'FileAttributes',
        'samples' => 'Sample',
        'file_samples' => 'FileSample',
    );

    public function setUp()
    {
        // echo "doing parent setup".PHP_EOL;
        parent::setUp();
        // echo "done with parent setup".PHP_EOL;
    }

    public function tearDown()
    {
        // echo "doing parent tearDown".PHP_EOL;
        parent::tearDown();
        // echo "done with parent tearDown".PHP_EOL;
        // var_dump($this->file_samples);
        $this->getFixtureManager()->truncateTable("file_sample");
        $this->getFixtureManager()->truncateTable("file_attributes");
    }

    public function testStoredReturnsDatasetId()
    {
        $dataset_id = 1;

        $daoUnderTest = new StoredDatasetFiles(
            $dataset_id,
            $this->getFixtureManager()->getDbConnection()
        );
        $this->assertEquals($dataset_id, $daoUnderTest->getDatasetId()) ;
    }

    public function testStoredReturnsDatasetDOI()
    {
        $dataset_id = 1;
        $doi = 100243;
        $daoUnderTest = new StoredDatasetFiles(
            $dataset_id,
            $this->getFixtureManager()->getDbConnection()
        );
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI()) ;
    }

    public function testStoredReturnsPaginatedDatasetFiles()
    {
        $dataset_id = 1;

        $expected = array(
            array(
                'id' => 1,
                'dataset_id' => 1,
                'name' => "readme.txt",
                'location' => 'ftp://foo.bar',
                'extension' => 'txt',
                'size' => 1322123045,
                'description' => 'just readme',
                'date_stamp' => '2015-10-12',
                'format' => 'TEXT',
                'type' => 'Text',
                'file_attributes' => array(
                    array("keyword" => "some value"),
                    array("number of lines" => "155"),
                ),
                'download_count' => 0,
            ),
            array(
                'id' => 2,
                'dataset_id' => 1,
                'name' => "readme.txt",
                'location' => 'ftp://foo.bar',
                'extension' => 'txt',
                'size' => -1,
                'description' => 'just readme',
                'date_stamp' => '2015-10-13',
                'format' => 'TEXT',
                'type' => 'Text',
                'file_attributes' => [],
                'download_count' => 0,
            ),
        );

        $daoUnderTest = new StoredDatasetFiles(
            $dataset_id,
            $this->getFixtureManager()->getDbConnection()
        );
        $this->assertEquals([$expected[1]], $daoUnderTest->getDatasetFiles(1,1)) ;
        $this->assertEquals([$expected[0]], $daoUnderTest->getDatasetFiles(1,0)) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetFiles(2)) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetFiles("ALL",0)) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetFiles()) ;
    }

    public function testStoredReturnsDatasetFilesSamples()
    {
        $dataset_id = 1;

        $expected = array(
                        array(
                            'sample_id' => 1,
                            'sample_name' => "Sample 1",
                            'file_id' => 1,
                        ),
                        array(
                            'sample_id' => 2,
                            'sample_name' => "Sample 2",
                            'file_id' => 1,
                        ),
                        array(
                            'sample_id' => 3,
                            'sample_name' => "Sample 3",
                            'file_id' => 1,
                        ),
                        array(
                            'sample_id' => 4,
                            'sample_name' => "Sample 4",
                            'file_id' => 1,
                        ),
                        array(
                            'sample_id' => 5,
                            'sample_name' => "Sample 5",
                            'file_id' => 2,
                        ),
                        array(
                            'sample_id' => 6,
                            'sample_name' => "Sample 6",
                            'file_id' => 2,
                        ),
                        array(
                            'sample_id' => 7,
                            'sample_name' => "Sample 7",
                            'file_id' => 2,
                        ),
                    );

        $daoUnderTest = new StoredDatasetFiles(
            $dataset_id,
            $this->getFixtureManager()->getDbConnection()
        );
        $this->assertEquals($expected, $daoUnderTest->getDatasetFilesSamples()) ;
    }
}
