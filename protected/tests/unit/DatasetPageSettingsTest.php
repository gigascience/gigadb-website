<?php
/**
 * Unit tests for DatasetPageSettings
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class DatasetPageSettingsTest extends CDbTestCase
{
	protected $fixtures=array(
        'datasets'=>'Dataset',
        'authors'=>'Author',
        'dataset_author'=>'DatasetAuthor',
    );

	public function testGetPageTypeNullModel()
	{
		$model = null;
		$sut = new DatasetPageSettings($model);

		$pageType = $sut->getPageType();
		$this->assertEquals("invalid", $pageType);
	}

	public function testGetPageTypeInvalid()
	{
		$model = new Dataset();
		$sut = new DatasetPageSettings($model);

		$pageType = $sut->getPageType();
		$this->assertEquals("invalid", $pageType);
	}

	public function testGetPageTypePublic()
	{
		$model = Dataset::model()->findByPk(1);
		$sut = new DatasetPageSettings($model);

		$pageType = $sut->getPageType();
		$this->assertEquals("public", $pageType);
	}

	public function testGetPageTypeHidden()
	{
		$model = Dataset::model()->findByPk(1);
		$model->upload_status = "Private";
		$sut = new DatasetPageSettings($model);

		$pageType = $sut->getPageType();
		$this->assertEquals("hidden", $pageType);
	}


	public function testGetPageTypeDraft()
	{
		$model = Dataset::model()->findByPk(1);
		$model->upload_status = "DataPending";
		$sut = new DatasetPageSettings($model);

		$pageType = $sut->getPageType();
		$this->assertEquals("draft", $pageType);
	}

	public function testGetPageTypeMockup()
	{
		$model = Dataset::model()->findByPk(1);
		$model->upload_status = "Submitted";
		$sut = new DatasetPageSettings($model);

		$pageType = $sut->getPageType();
		$this->assertEquals("mockup", $pageType);
	}

	public function testGetFileSettingsNoCookies()
	{
		$defaultColumns = array('name','description','size', 'type_id', 'format_id', 'location', 'date_stamp','sample_id','attribute');
		$defaultPageSize = 10 ;
		$sut = new DatasetPageSettings($model);
		$fileSettings = $sut->getFileSettings(null, DatasetPageSettings::MOCKUP_COLUMNS);
		$this->assertEquals($defaultColumns, $fileSettings["columns"]);
		$this->assertEquals($defaultPageSize, $fileSettings["pageSize"]);
	}

	public function testGetFileSettingsWithCookies()
	{
		$columns = array('name','size', 'type_id', 'format_id');
		$pageSize = 20 ;
		$cookies = new CMap();
		$cookie = new CHttpCookie('file_setting', json_encode(array('setting'=> $columns, 'page'=>$pageSize)));
        $cookie->expire = time() + (60*60*24*30);
        $cookies['file_setting'] = $cookie;
		$sut = new DatasetPageSettings($model);
		$fileSettings = $sut->getFileSettings($cookies);
		$this->assertEquals($columns, $fileSettings["columns"]);
		$this->assertEquals($pageSize, $fileSettings["pageSize"]);		
	}

	public function testSetFileSettingsExistingCookie()
	{
		$defaultColumns = array('name','size', 'type_id', 'format_id', 'location', 'date_stamp','sample_id');
		$defaultPageSize = 10 ;
		$columns = array('name','size', 'type_id', 'format_id');
		$pageSize = 5 ;
		$sut = new DatasetPageSettings($model);
		$cookies = new CMap(); //the superclass of CCookieCollection
		$cookies['file_setting'] = new CHttpCookie('file_setting',json_encode(array('setting'=> $defaultColumns, 'page'=>$defaultPageSize)));
		$sut->setFileSettings($columns, $pageSize, $cookies);
		$this->assertEquals($columns, json_decode($cookies['file_setting']->value,true)["setting"]);
		$this->assertEquals($pageSize, json_decode($cookies['file_setting']->value,true)["page"]);		
	}

	public function testSetFileSettingsNoCookie()
	{
		$defaultColumns = array('name','size', 'type_id', 'format_id', 'location', 'date_stamp','sample_id');
		$defaultPageSize = 10 ;
		$columns = array('name','size', 'type_id', 'format_id');
		$pageSize = 5 ;
		$sut = new DatasetPageSettings($model);
		$cookies = new CMap(); //the superclass of CCookieCollection
		$fileSettings = $sut->setFileSettings($columns, $pageSize, $cookies);
		$this->assertTrue(isset($fileSettings) && is_array($fileSettings));
		$this->assertEquals(["columns" => $columns, "pageSize" => $pageSize], $fileSettings);
		$this->assertEquals($columns, json_decode($cookies['file_setting']->value,true)["setting"]);
		$this->assertEquals($pageSize, json_decode($cookies['file_setting']->value,true)["page"]);	
	}

	public function testGetSampleSettingsNoCookies()
	{
		$defaultColumns = array('name', 'taxonomic_id', 'genbank_name', 'scientific_name', 'common_name', 'attribute');
		$defaultPageSize = 10 ;
		$sut = new DatasetPageSettings($model);
		$sampleSettings = $sut->getSampleSettings();
		$this->assertEquals($defaultColumns, $sampleSettings["columns"]);
		$this->assertEquals($defaultPageSize, $sampleSettings["pageSize"]);
	}

	public function testGetSampleSettingsWithCookies()
	{
		$columns = array('name', 'taxonomic_id', 'genbank_name');
		$pageSize = 20 ;
		$cookies = new CMap();
		$cookie = new CHttpCookie('sample_setting', json_encode(array('columns'=> $columns, 'page'=>$pageSize)));
        $cookie->expire = time() + (60*60*24*30);
        $cookies['sample_setting'] = $cookie;
		$sut = new DatasetPageSettings($model);
		$sampleSettings = $sut->getSampleSettings($cookies);
		$this->assertEquals($columns, $sampleSettings["columns"]);
		$this->assertEquals($pageSize, $sampleSettings["pageSize"]);		
	}

	public function testSetSampleSettingsExistingCookie()
	{
		$defaultColumns = array('name', 'taxonomic_id', 'genbank_name', 'scientific_name', 'common_name', 'attribute');
		$defaultPageSize = 10 ;
		$columns = array('name', 'taxonomic_id', 'genbank_name');
		$pageSize = 5 ;
		$sut = new DatasetPageSettings($model);
		$cookies = new CMap(); //the superclass of CCookieCollection
		$cookies['sample_setting'] = new CHttpCookie('sample_setting',json_encode(array('columns'=> $defaultColumns, 'page'=>$defaultPageSize)));
		$sampleSettings = $sut->setSampleSettings($columns, $pageSize, $cookies);
		$this->assertTrue(isset($sampleSettings) && is_array($sampleSettings));
		$this->assertEquals(["columns" => $columns, "pageSize" => $pageSize], $sampleSettings);
		$this->assertEquals($columns, json_decode($cookies['sample_setting']->value,true)["columns"]);
		$this->assertEquals($pageSize, json_decode($cookies['sample_setting']->value,true)["page"]);		
	}

	public function testSetSampleSettingsNoCookie()
	{
		$defaultColumns = array('name', 'taxonomic_id', 'genbank_name', 'scientific_name', 'common_name', 'attribute');
		$defaultPageSize = 10 ;
		$columns = array('name', 'taxonomic_id', 'genbank_name');
		$pageSize = 5 ;
		$sut = new DatasetPageSettings($model);
		$cookies = new CMap(); //the superclass of CCookieCollection
		$sut->setSampleSettings($columns, $pageSize, $cookies);
		$this->assertEquals($columns, json_decode($cookies['sample_setting']->value,true)["columns"]);
		$this->assertEquals($pageSize, json_decode($cookies['sample_setting']->value,true)["page"]);	
	}
}
?>