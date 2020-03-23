<?php

/**
 * Component to manage Dataset page and form settings, primarily the page type
 * 
 * That is, based on whether the page is the editing form or the viewing page
 * and on the upload status, the component will expose functions to detect if 
 * the page is hidden, public, a mockup or invalid and to generate a hidden or mockup page
 *
 * @author Ken Cho & Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class DatasetPageSettings extends yii\base\BaseObject
{
	/** @var constant to assign the default column names of files table to a constant */
    const VIEW_DEFAULT_FILE_COLUMNS = ['name', 'description', 'type_id' , 'size', 'attribute', 'location'];

	/** @var DatasetDAO $_dataset The dao class for getting dataset info*/
	private $_dao;

	/** @var null|Dataset $_model dataset model for which the page is configured. Can be null */
	private $_model;

	/** @var string $_pageType mode: update or view */
	private $_pageType;


	public function __construct(?Dataset $model, DatasetDAO $dao = null, $config = [])
	{
		parent::__construct();
		$this->_dao = $dao;
		$this->_model = $model;
		if ($this->_model && !$this->_model->isNewRecord && "Published" !== $this->_model->upload_status
				&& "DataAvailableForReview" !== $this->_model->upload_status) {
			$this->_pageType = "hidden";
		}
		elseif($this->_model && !$this->_model->isNewRecord && "DataAvailableForReview" === $this->_model->upload_status) {
			$this->_pageType = "mockup";
		}
		elseif($this->_model && !$this->_model->isNewRecord){
			$this->_pageType = "public";
		}
		else {
			$this->_pageType = "invalid";
		}
	}

	/**
	 * Return the type of page (public|hidden|mockup) to be used in controller and view template
	 *
	 * @return string the type of page: public|hidden|mockup
	 */
	public function getPageType(): string
	{
		return $this->_pageType;
	}
}
?>