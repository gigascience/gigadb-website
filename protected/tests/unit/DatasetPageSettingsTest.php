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

	public function testGetPageTypeMockup()
	{
		$model = Dataset::model()->findByPk(1);
		$model->upload_status = "DataAvailableForReview";
		$sut = new DatasetPageSettings($model);

		$pageType = $sut->getPageType();
		$this->assertEquals("mockup", $pageType);
	}
}
?>