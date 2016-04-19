<?php

/**
 * SearchForm class.
 * SearchForm is the data structure for keeping
 */
class SearchForm extends CFormModel {
	public $keyword = '';
	public $criteria = '';
	public $tab = "";
	public $query_result = "";


    public $type = array();

    /*
	 * Dataset
	 */
	public $dataset_type = array();
	public $publisher = array();
	public $project = array();
	public $pubdate_from = "";
	public $pubdate_to = "";
	public $moddate_from = "";
	public $moddate_to = "";
	public $author_id = "";

	public $common_name = array();
	public $external_link_type = array();
	public $exclude = "";

	/**
	 * File
	 */

	public $file_type=array();
    public $file_format=array();   

    public $reldate_from="";
    public $reldate_to="";
    public $size_from="";
    public $size_to="";
    public $size_from_unit="";
    public $size_to_unit="";

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules() {
		return array(
			array('keyword', 'required'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels() {
		return array(
			'keyword' => 'KeyWord',
		);
	}

	public function getParams($excludeId = false) {
		$excludes = explode(",", $this->exclude);

		if ($excludeId && !in_array($excludeId, $excludes)) {
			$excludes[] = $excludeId;
		}

		$params=array();
		$params[]='search/index';
		$params['keyword']=$this->keyword; // Keyword is mandatory

		if(!empty($this->type)){
			$params['type']=$this->type;
		}

		if(!empty($this->dataset_type)){
			$params['dataset_type']=$this->dataset_type;
		}
		if (!empty($this->publisher)) {
			$params['publisher'] = $this->publisher;
		}
		if (!empty($this->project)) {
			$params['project'] = $this->project;
		}
		if (!empty($this->pubdate_from)) {
			$params['pubdate_from'] = $this->pubdate_from;
		}
		if (!empty($this->pubdate_to)) {
			$params['pubdate_to'] = $this->pubdate_to;
		}
		if (!empty($this->moddate_from)) {
			$params['moddate_from'] = $this->moddate_from;
		}
		if (!empty($this->moddate_to)) {
			$params['moddate_to'] = $this->moddate_to;
		}
		if (!empty($this->common_name)) {
			$params['common_name'] = $this->common_name;
		}
		if (!empty($excludes) && $excludeId) {
			$params['exclude'] = implode(",", $excludes);
		}
		if (!empty($this->external_link_type)) {
			$params['external_link_type'] = $this->external_link_type;
		}
		if (!empty($this->file_type)) {
			$params['file_type'] = $this->file_type;
		}
		if (!empty($this->file_format)) {
			$params['file_format'] = $this->file_format;
		}
		if (!empty($this->reldate_from)) {
			$params['reldate_from'] = $this->reldate_from;
		}
		if (!empty($this->reldate_to)) {
			$params['reldate_to'] = $this->reldate_to;
		}
		if (!empty($this->size_from)) {
			$params['size_from'] = $this->size_from;
		}
		if (!empty($this->size_to)) {
			$params['size_to'] = $this->size_to;
		}
		if (!empty($this->size_to_unit)) {
			$params['size_to_unit'] = $this->size_to_unit;
		}
		if (!empty($this->size_from_unit)) {
			$params['size_from_unit'] = $this->size_from_unit;
		}
		if (!empty($this->type)) {
			$params['type'] = $this->type;
		}

		return $params;
	}

}
