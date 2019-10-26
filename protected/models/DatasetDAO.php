<?php

/**
 * Class to interact with persisted Dataset elements on behalf of the service layer
 *
 *
 * @uses DatasetAttributesFactory.php
 * @uses DatasetAttributes.php
 * @uses Attribute.php
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class DatasetDAO
{

	/** @var DatasetAttributesFactory contains a factory instance for making DatasetAttributes. */
	protected $dataset_attr_factory;

	/**
	 * Initializes this class with the given option
	 *
	 * @param DatasetAttributesFactory $da_factory injected factory for making new DatasetAttribute instances
	 */
	public function __construct($da_factory)
	{
		$this->dataset_attr_factory = $da_factory;
	}


	/**
	 * Remove DatasetAttributes entries in the database for 'keyword' attribute and given dataset_id
	 *
	 * @param int $dataset_id
	 */
	public function removeKeywordsFromDatabaseForDatasetId($dataset_id)
	{
		$keyword_attribute = Attribute::model()->findByAttributes(array('attribute_name'=>'keyword'));

		$datasetAttributes = DatasetAttributes::model()->findAllByAttributes(
								array('dataset_id'=>$dataset_id,'attribute_id'=>$keyword_attribute->id)
							);

		foreach ($datasetAttributes as $keyword) {
			$keyword->delete();
		}
	}

	/**
	 * Add DatasetAttributes entries in the database for 'keyword' attribute and given dataset_id
	 * and keywords string
	 *
	 * @param int $dataset_id
	 * @param string $post_keywords_string
	 */
	public function addKeywordsToDatabaseForDatasetIdAndString($dataset_id, $post_keywords_string)
	{
		$keyword_attribute = Attribute::model()->findByAttributes(array('attribute_name'=>'keyword'));
		$keywords_array = array_filter(explode(',', $post_keywords_string));

		foreach ($keywords_array as $keyword) {
			$this->dataset_attr_factory->create();
			$this->dataset_attr_factory->setAttributeId($keyword_attribute->id);
			$this->dataset_attr_factory->setDatasetId($dataset_id);
			$this->dataset_attr_factory->setValue( trim($keyword) );
			$this->dataset_attr_factory->save();
		}

	}

	/**
	 * Update a dataset's upload_status from one status to another
	 *
	 * If the fromStatus doesn't exist, it is noop and return false
	 *
	 * @param int $dataset_id
	 * @param string $fromStatus upload status to transition from
	 * @param string $toStatus upload status to transition to
	 * @param string $comment description for curation_log entry
	 *
	 * @return bool whether the transition was enacted or not
	 */
	public function transitionStatus(int $dataset_id, string $fromStatus, string $toStatus, string $comment = null): bool
	{
		$dataset = Dataset::model()->findByPk($dataset_id);
		if ($fromStatus !== $dataset->upload_status) {
			return false;
		}
		if (null === $toStatus) {
			return false;
		}
		$dataset->upload_status = $toStatus;
		if ( $dataset->save() ) {
			return true;
		}
		return false;
	}
}

?>