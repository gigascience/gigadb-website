<?php

declare(strict_types=1);

require_once __DIR__ . '/LoadingFixtureTrait.php';
require_once __DIR__ . '/CdbUnit.php';

use Codeception\Test\Unit;

/**
 * Unit tests for StoredDatasetFiles to retrieve from storage, the files for associated dataset
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetFilesTest extends CdbUnit
{
    use LoadingFixtureTrait;

    public function _before()
    {
        $db = $this->getModule('Db')->_getDbh();
        $db->exec('TRUNCATE TABLE species CASCADE');
        $db->exec('TRUNCATE TABLE dataset CASCADE');
        $db->exec('TRUNCATE TABLE gigadb_user CASCADE');
        $db->exec('TRUNCATE TABLE attribute CASCADE');
        $db->exec('TRUNCATE TABLE file_format CASCADE');
        $db->exec('TRUNCATE TABLE file_type CASCADE');
        $db->exec('TRUNCATE TABLE file CASCADE');
        $db->exec('TRUNCATE TABLE file_attributes CASCADE');
        $db->exec('TRUNCATE TABLE sample CASCADE');
        $db->exec('TRUNCATE TABLE file_sample CASCADE');

        $this->loadFixture('gigadb_user', \User::class);
        $this->loadFixture('species', \Species::class);
        $this->loadFixture('dataset', \Dataset::class);
        $this->loadFixture('attribute', \Attributes::class);
        $this->loadFixture('file_format', \FileFormat::class);
        $this->loadFixture('file_type', \FileType::class);
        $this->loadFixture('file', \File::class);
        $this->loadFixture('file_attributes', \FileAttributes::class);
        $this->loadFixture('sample', \Sample::class);
        $this->loadFixture('file_sample', \FileSample::class);

        parent::_before();
    }

    public function testStoredReturnsDatasetId()
    {
        $dataset_id = 1;

        $daoUnderTest = new StoredDatasetFiles(
            $dataset_id,
           $this->cdbConnection
        );
        $this->assertEquals($dataset_id, $daoUnderTest->getDatasetId()) ;
    }

    public function testStoredReturnsDatasetDOI()
    {
        $dataset_id = 1;
        $doi = 100243;
        $daoUnderTest = new StoredDatasetFiles(
            $dataset_id,
           $this->cdbConnection
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
                    array('number of lines' => '155'),
                    array("keyword" => "some value"),
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
           $this->cdbConnection
        );
        $this->assertEquals([$expected[1]], $daoUnderTest->getDatasetFiles("1", 1)) ;
        $this->assertEquals([$expected[0]], $daoUnderTest->getDatasetFiles("1", 0)) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetFiles("2")) ;
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
           $this->cdbConnection
        );
        $this->assertEquals($expected, $daoUnderTest->getDatasetFilesSamples()) ;
    }
}
