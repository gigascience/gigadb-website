<?php

/**
 * Class that offer controllers access to services related to Attributes
 *
 * It aims to hide storage concerns from the view and controllers
 * and to isolate storage layer from presentation concerns.
 * Currently it only supports the Keyword attribute for DatasetAttributes
 *
 * @uses DatasetDao.php
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class AttributeService extends CApplicationComponent
{

	/** @var DatasetDao $dataset_dao handle to data layer for Dataset */
	private $dataset_dao;

	/**
	 * Initializes this class with the given option
	 *
	 * @param DatasetDao $dataset_dao injected insance of DatasetDAO
	 */
	public function __construct($dataset_dao = null)
	{
		if ($dataset_dao && $dataset_dao instanceof DatasetDAO) {
			$this->dataset_dao = $dataset_dao;
		}
		else {
			$da_factory = new DatasetAttributesFactory();
			$this->dataset_dao = new DatasetDAO();
			$this->dataset_dao->DatasetAttrFactory = $da_factory ;
		}
	}
	/**
	 * Replace keywords in the database with supplied string of keywords for a given dataset
	 * All keywords for the dataset are removed first, then the new ones are sanitized and added
	 *
	 * This method uses a database transaction wrapping the keyword removal and adding methods
	 * However the method is sometimes called in context that are already in a transaction.
	 * We need to use the outer parent transaction when that's the case.
	 *
	 * @param int $dataset_id
	 * @param string $keywords
	 */
	public function replaceKeywordsForDatasetIdWithString($dataset_id, $keyword_string)
	{
		$sanitized_keywords = trim( filter_var( $keyword_string, FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
		$transaction = Yii::app()->db->getCurrentTransaction();
	    if ($transaction !== null) {
	        // Transaction already started outside
	        $transaction = null;
	    }
	    else {
	        // There is no outer transaction, creating a local one
	        $transaction = Yii::app()->db->beginTransaction();
	    }


		try {
			$this->dataset_dao->removeKeywordsFromDatabaseForDatasetId($dataset_id);
			$this->dataset_dao->addKeywordsToDatabaseForDatasetIdAndString($dataset_id, $sanitized_keywords);
		    if ($transaction !== null) {
	          $transaction->commit();
		    }
		}
		catch (Exception $e) {
		    if ($transaction !== null) {
	          $transaction->rollback();
		    }
		}
	}
}

?>