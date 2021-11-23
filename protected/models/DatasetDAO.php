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
class DatasetDAO extends yii\base\BaseObject
{

	/** @var DatasetAttributesFactory $_datasetAttrFactory contains a factory instance for making DatasetAttributes. */
	protected $_datasetAttrFactory;

	/** @var string $_identifier property to hold a DOI to be manipulated/queried by DAO func. */
	protected $_identifier;

	/**
	 * Getter for _datasetAttrFactory
	 * @return DatasetAttribute
	 */
	public function getDatasetAttrFactory(): DatasetAttributesFactory
	{
	    return $this->_datasetAttrFactory;
	}

	/**
	 * Setter for _datasetAttrFactory
	 * @param DatasetAttribute
	 */
	public function setDatasetAttrFactory(DatasetAttributesFactory $datasetAttributeFactory): void
	{
	    $this->_datasetAttrFactory = $datasetAttributeFactory;
	}

	/**
	 * Getter for _identifier
	 * @return string
	 */
	public function getIdentifier(): string
	{
	    return $this->_identifier;
	}

	/**
	 * Setter for _identifier
	 * @param string
	 */
	public function setIdentifier(string $identifier): void
	{
	    $this->_identifier = $identifier;
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
			$this->_datasetAttrFactory->create();
			$this->_datasetAttrFactory->setAttributeId($keyword_attribute->id);
			$this->_datasetAttrFactory->setDatasetId($dataset_id);
			$this->_datasetAttrFactory->setValue( trim($keyword) );
			$this->_datasetAttrFactory->save();
		}

	}

	/**
	 * Update a dataset's upload_status from one status to another
	 *
	 * If the fromStatus doesn't exist, it is noop and return false
	 *
	 * @param string $fromStatus upload status to transition from
	 * @param string $toStatus upload status to transition to
	 * @param string $comment description for curation_log entry
	 *
	 * @return bool whether the transition was enacted or not
	 */
	public function transitionStatus(string $fromStatus, string $toStatus, string $comment = null): bool
	{

		$dataset = Dataset::model()->findByAttributes(["identifier" => $this->_identifier]);
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

	/**
	 * return title and status for given dataset
	 *
	 * @return array|null return associate array of title, status or null
	 */
	public function getTitleAndStatus(): ?array
	{
		$dataset = Dataset::model()->findByAttributes(["identifier" => $this->_identifier]);
		return array("title" =>$dataset->title, "status" => $dataset->upload_status);
	}

	/**
	 * return user who submitted the dataset
	 *
	 * @return User||null return User
	 */
	public function getSubmitter(): ?User
	{
		$dataset = Dataset::model()->findByAttributes(["identifier" => $this->_identifier]);
		return $dataset->submitter;
	}

	/**
	 * return Id for given identifier (DOI)
	 *
	 * @return int return dataset DB id
	 */
	public function getId(): int
	{
		$dataset = Dataset::model()->findByAttributes(["identifier" => $this->_identifier]);
		return $dataset->id;
	}

	/**
	 * Return the next Dataset or the current one if none
	 *
	 * TODO: identifier is of type string, change the comparison to use either id or string compare
	 * TODO: cache the result
	 * TODO: to be used through DatasetPageAssembly is better than directly in DatasetController
	 *
	 * @return ?Dataset if it exists, it's the next dataset when sorted by identifier ascending
	 */
	public function getNextDataset(): ?Dataset
	{
		$result = Dataset::model()->findBySql("select id, identifier,title from dataset where identifier > '" . $this->_identifier . "' and upload_status='Published' order by identifier asc limit 1;");
        
        return $result;
	}

	/**
	 * Return the previous Dataset or the current one if none
	 *
	 * TODO: identifier is of type string, change the comparison to use either id or string compare
	 * TODO: cache the result
	 * TODO: to be used through DatasetPageAssembly is better than directly in DatasetController
	 *
	 * @return ?Dataset if it exists, it's the next dataset when sorted by identifier ascending
	 */
	public function getPreviousDataset(): ?Dataset
	{
		$result = Dataset::model()->findBySql("select id, identifier,title from dataset where identifier < '" . $this->_identifier . "' and upload_status='Published' order by identifier desc limit 1;");
        
        return $result;
	}

	/**
	 * Return the next Dataset or the current one if none
	 *
	 * TODO: identifier is of type string, change the comparison to use either id or string compare
	 * TODO: cache the result
	 * TODO: to be used through DatasetPageAssembly is better than directly in DatasetController
	 *
	 * @return Dataset the first dataset when sorted by identifier ascending
	 */
	public function getFirstDataset(): Dataset
	{
		$result = Dataset::model()->findBySql("select id, identifier,title from dataset where upload_status='Published' order by identifier asc limit 1;");
        
        return $result;
	}

}

?>