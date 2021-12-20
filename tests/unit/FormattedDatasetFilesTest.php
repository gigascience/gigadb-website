<?php
/**
 * Unit tests for FormattedDatasetFiles to present the files associated to a dataset
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FormattedDatasetFilesTest extends CTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function testFormattedReturnsDatasetId()
    {
        $dataset_id = 6;
        $pageSize= 10 ;
        // create a mock for the CachedDatasetFiles
        $cachedDatasetFiles = $this->getMockBuilder(CachedDatasetFiles::class)
                         ->setMethods(['getDatasetId'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation
        $cachedDatasetFiles->expects($this->once())
                 ->method('getDatasetId')
                 ->willReturn(6);


        $daoUnderTest = new FormattedDatasetFiles($pageSize, $cachedDatasetFiles);
        $this->assertEquals($dataset_id, $daoUnderTest->getDatasetId() ) ;
    }

    public function testFormattedReturnsDatasetDOI()
    {
        $dataset_id = 6;
        $pageSize= 10 ;
        $doi = "100044";
         // create a mock for the CachedDatasetFiles
        $cachedDatasetFiles = $this->getMockBuilder(CachedDatasetFiles::class)
                         ->setMethods(['getDatasetDOI'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation
        $cachedDatasetFiles->expects($this->once())
                 ->method('getDatasetDOI')
                 ->willReturn("100044");


        $daoUnderTest = new FormattedDatasetFiles($pageSize, $cachedDatasetFiles);
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI() ) ;
    }

    /**
     * test that we get dataset files whose attribute are suited for presentation (especially size and name)
     *
     */
    public function testFormattedReturnsDatasetFiles()
    {
        $dataset_id = 1;
        $pageSize = 2;

        $source = array(
            array(
                'id' => 1,
                'dataset_id' => 1,
                'name' => "readme.txt",
                'location'=>'ftp://foo.bar',
                'extension'=>'txt',
                'size'=>1322123045,
                'description'=>'just readme',
                'date_stamp' => '2015-10-12',
                'format' => 'TEXT',
                'type' => 'Text',
                'file_attributes' => array(
                    array("keyword" => "some value"),
                    array("number of lines" => "155"),
                ),
                'download_count'=>0,
            ),
            array(
                'id' => 2,
                'dataset_id' => 1,
                'name' => "readme2.txt",
                'location'=>'ftp://foo2.bar',
                'extension'=>'txt',
                'size'=>-1,
                'description'=>'just readme 2',
                'date_stamp' => '2015-10-13',
                'format' => 'TEXT',
                'type' => 'Text',
                'file_attributes' => [],
                'download_count'=>0,
            ),
        );

        $expected = array(
                        array(
                            'id' => 1,
                            'dataset_id' => 1,
                            'name' => "readme.txt",
                            'location'=>'ftp://foo.bar',
                            'extension'=>'txt',
                            'size'=>1322123045,
                            'description'=>'just readme',
                            'date_stamp' => '2015-10-12',
                            'format' => 'TEXT',
                            'type' => 'Text',
                            'file_attributes' => array(
                                array("keyword" => "some value"),
                                array("number of lines" => "155"),
                            ),
                            'download_count'=>0,
                            'nameHtml' => "<div title=\"just readme\"><a href=\"ftp://foo.bar\" target='_blank'>readme.txt</a></div>",
                            'sizeUnit'=>'1.23 GiB',
                            'attrDesc' => "keyword: some value<br>number of lines: 155<br>",
                        ),
                        array(
                            'id' => 2,
                            'dataset_id' => 1,
                            'name' => "readme2.txt",
                            'location'=>'ftp://foo2.bar',
                            'extension'=>'txt',
                            'size'=>-1,
                            'description'=>'just readme 2',
                            'date_stamp' => '2015-10-13',
                            'format' => 'TEXT',
                            'type' => 'Text',
                            'file_attributes' => [],
                            'download_count'=>0,
                            'nameHtml' => "<div title=\"just readme 2\"><a href=\"ftp://foo2.bar\" target='_blank'>readme2.txt</a></div>",
                            'sizeUnit'=>'-1',
                            'attrDesc' => "",
                        ),
                    );

        // create a mock for the CachedDatasetFiles
        $cachedDatasetFiles = $this->getMockBuilder(CachedDatasetFiles::class)
                         ->setMethods(['getDatasetFiles'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation
        $cachedDatasetFiles->expects($this->exactly(2))
                 ->method('getDatasetFiles')
                 ->willReturn( $source );


        $daoUnderTest = new FormattedDatasetFiles($pageSize, $cachedDatasetFiles);
        $this->assertEquals($expected, $daoUnderTest->getDatasetFiles()) ;
        $this->assertEquals(count($expected), $daoUnderTest->countDatasetFiles()) ;//_nbfiles not set
        $this->assertEquals(count($expected), $daoUnderTest->countDatasetFiles()) ;//_nbfiles set
    }

    /**
     * test that we get files data from cache, and returns a CArrayDataProvider, a CPagination object, and a CSort object
     *
     */
    public function testFormattedReturnsDataProvider()
    {
        $dataset_id = 1;
        $pageSize = 2;

        $expected = array(
                        array(
                            'id' => 1,
                            'dataset_id' => 1,
                            'name' => "readme.txt",
                            'location'=>'ftp://foo.bar',
                            'extension'=>'txt',
                            'size'=>1322123045,
                            'description'=>'just readme',
                            'date_stamp' => '2015-10-12',
                            'format' => 'TEXT',
                            'type' => 'Text',
                            'file_attributes' => array(
                                array("keyword" => "some value"),
                                array("number of lines" => "155"),
                            ),
                            'download_count'=>0,
                            'nameHtml' => "<div title=\"just readme\"><a href=\"ftp://foo.bar\" target='_blank'>readme.txt</a></div>",
                            'sizeUnit'=>'1.23 GiB',
                            'attrDesc' => "keyword: some value<br>number of lines: 155<br>",
                        ),
                        array(
                            'id' => 2,
                            'dataset_id' => 1,
                            'name' => "readme2.txt",
                            'location'=>'ftp://foo2.bar',
                            'extension'=>'txt',
                            'size'=>-1,
                            'description'=>'just readme 2',
                            'date_stamp' => '2015-10-13',
                            'format' => 'TEXT',
                            'type' => 'Text',
                            'file_attributes' => [],
                            'download_count'=>0,
                            'nameHtml' => "<div title=\"just readme 2\"><a href=\"ftp://foo2.bar\" target='_blank'>readme2.txt</a></div>",
                            'sizeUnit'=>'-1',
                            'attrDesc' => "",
                        ),
                    );

        // create a mock for the CachedDatasetFiles
        $cachedDatasetFiles = $this->getMockBuilder(CachedDatasetFiles::class)
                         ->setMethods(['getDatasetFiles'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation
        $cachedDatasetFiles->expects($this->exactly(2))
                 ->method('getDatasetFiles')
                 ->willReturn( $expected );


        $daoUnderTest = new FormattedDatasetFiles($pageSize, $cachedDatasetFiles);
        $this->assertEquals($expected, $daoUnderTest->getDataProvider()->getData()) ;
        $this->assertEquals(2, $daoUnderTest->getDataProvider()->getPagination()->getPageSize() ) ;
    }

    /**
     * Test the function that the list of samples associate to dataset's files with correct formatting
     *
     */
    public function testFormattedReturnsFormatDatasetFilesSamples()
    {
        $pageSize = 10;

        $sample_threshold = 3 ;

        $source_files = array(
                        array(
                            'id' => 1,
                            'dataset_id' => 1,
                            'name' => "readme.txt",
                            'location'=>'ftp://foo.bar',
                            'extension'=>'txt',
                            'size'=>1322123045,
                            'description'=>'just readme',
                            'date_stamp' => '2015-10-12',
                            'format' => 'TEXT',
                            'type' => 'Text',
                            'file_attributes' => array(
                                array("keyword" => "some value"),
                                array("number of lines" => "155"),
                            ),
                            'download_count'=>0,
                        ),
                        array(
                            'id' => 2,
                            'dataset_id' => 1,
                            'name' => "readme.txt",
                            'location'=>'ftp://foo.bar',
                            'extension'=>'txt',
                            'size'=>-1,
                            'description'=>'just readme',
                            'date_stamp' => '2015-10-13',
                            'format' => 'TEXT',
                            'type' => 'Text',
                            'file_attributes' => [],
                            'download_count'=>0,
                        ),
                    );
        $source_samples = array(
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

        $expected = array(
            array(
                'file_id' => 1,
                'visible' => '<span class="js-short-1">Sample 1</span>',
                'hidden' => '<span class="js-long-1" style="display: none;">Sample 1, Sample 2, Sample 3, Sample 4</span>',
                'more_link' => '<a href="#" class="js-desc" data="1">+</a>',
            ),
            array(
                'file_id' => 2,
                'visible' => '<span class="js-short-2">Sample 5, Sample 6, Sample 7</span>',
                'hidden' => '',
                'more_link' => '',
            ),
        );

        // create a mock for the CachedDatasetFiles
        $cachedDatasetFiles = $this->getMockBuilder(CachedDatasetFiles::class)
                         ->setMethods(['getDatasetFilesSamples', 'getDatasetFiles'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectations
        $cachedDatasetFiles->expects($this->exactly(2))
                 ->method('getDatasetFiles')
                 ->willReturn( $source_files );

        $cachedDatasetFiles->expects($this->exactly(3))
                 ->method('getDatasetFilesSamples')
                 ->willReturn( $source_samples );



        $daoUnderTest = new FormattedDatasetFiles($pageSize, $cachedDatasetFiles);
        $this->assertEquals($expected, $daoUnderTest->formatDatasetFilesSamples($sample_threshold)) ;
        $this->assertEquals([$expected[1]], $daoUnderTest->formatDatasetFilesSamples($sample_threshold,2)) ;
    }


}
?>