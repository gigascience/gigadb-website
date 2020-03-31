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

	/** @var string $_fileSettings */
	private $_fileSettings;

	public function __construct(?Dataset $model, DatasetDAO $dao = null, $config = [])
	{
		parent::__construct();
		$this->_dao = $dao;
		$this->_model = $model;
		if ($this->_model && !$this->_model->isNewRecord && "Published" !== $this->_model->upload_status
				&& "Submitted" !== $this->_model->upload_status) {
			$this->_pageType = "hidden";
		}
		elseif($this->_model && !$this->_model->isNewRecord && "Submitted" === $this->_model->upload_status) {
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

	/**
	 * Return list of columns and page size that control display of files
	 *
	 * @param CMap $cookies collection of HTTP cookies (CCookieCollection is subclass of CMap)
	 * @return array columns and pageSize settings as associative array
	 */
	public function getFileSettings(CMap $cookies = null): array
	{
		// default values
		$fileSettings = [
			"setting" => self::VIEW_DEFAULT_FILE_COLUMNS,
			"page" => 10,
		];

		if( isset($cookies['file_setting']) ){
			$fileSettings = json_decode( $cookies['file_setting']->value , true);
		}

		return [ "columns" => $fileSettings['setting'], "pageSize" => $fileSettings['page'] ];
	}

	/**
	 * Store new columns and page setting in a cookie for files setting
	 *
	 * @param array $columns list of columns for the file table
	 * @param int $pageSize size of a page of the file table
	 * @param CMap $cookies collection of HTTP cookies (CCookieCollection is subclass of CMap)
	 * @return array columns and pageSize settings as associative array
	 */
	public function setFileSettings(array $columns, int $pageSize, CMap $cookies): array
	{
		if( isset($cookies['file_setting']) ){
			$cookies['file_setting']->value = json_encode([ "setting" => $columns, "page" => $pageSize]);
		}
		else {
				$cookie = new CHttpCookie('file_setting', json_encode(array('setting'=> $columns, 'page'=>$pageSize)));
		        $cookie->expire = time() + (60*60*24*30);
		        $cookies['file_setting'] = $cookie;
		}
		return [ "columns" => $columns, "pageSize" => $pageSize ];
	}

	/**
	 * Return list of columns and page size that control display of samples
	 *
	 * @param CMap $cookies collection of HTTP cookies (CCookieCollection is subclass of CMap)
	 * @return array columns and pageSize settings as associative array
	 */
	public function getSampleSettings(CMap $cookies = null): array
	{
		// default values
		$sampleSettings = [
			"columns" => array('name', 'taxonomic_id', 'genbank_name', 'scientific_name', 'common_name', 'attribute'),
			"page" => 10,
		];

		if( isset($cookies['sample_setting']) ){
			$sampleSettings = json_decode( $cookies['sample_setting']->value , true);
		}

		return [ "columns" => $sampleSettings['columns'], "pageSize" => $sampleSettings['page'] ];
	}

	/**
	 * Store new columns and page setting in a cookie for samples setting
	 *
	 * @param array $columns list of columns for the file table
	 * @param int $pageSize size of a page of the file table
	 * @param CMap $cookies collection of HTTP cookies (CCookieCollection is subclass of CMap)
	 * @return array columns and pageSize settings as associative array
	 */
	public function setSampleSettings(array $columns, int $pageSize, CMap $cookies): array
	{
		if( isset($cookies['sample_setting']) ){
			$cookies['sample_setting']->value = json_encode([ "columns" => $columns, "page" => $pageSize]);
		}
		else {
				$cookie = new CHttpCookie('sample_setting', json_encode(array('columns'=> $columns, 'page'=>$pageSize)));
		        $cookie->expire = time() + (60*60*24*30);
		        $cookies['sample_setting'] = $cookie;
		}
		return [ "columns" => $columns, "pageSize" => $pageSize ];
	}

}
?>