<?php
/**
 * Format and return a dataset's connections
 *
 *
 * @param CController $controller current controller so we can call render_file for special-case HTML snippet
 * @param DatasetConnectionsInterface $datasetConnections the adaptee class that return cached connections
 * @see DatasetConnectionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FormattedDatasetConnections extends DatasetComponents implements DatasetConnectionsInterface
{
	private $_cachedDatasetConnections;
	private $_controller;

	public function __construct (CController $controller, DatasetConnectionsInterface $datasetConnections)
	{
		parent::__construct();
		$this->_cachedDatasetConnections = $datasetConnections;
		$this->_controller = $controller;
	}

	/**
	 * return the dataset id
	 *
	 * @return int
	 */
	public function getDatasetId(): int
	{
		return $this->_cachedDatasetConnections->getDatasetId();
	}

	/**
	 * return the dataset identifier (DOI)
	 *
	 * @return string
	 */
	public function getDatasetDOI(): string
	{
		return $this->_cachedDatasetConnections->getDatasetDOI();
	}

	/**
	 * retrieval of related datasets in order to show them on th view layer.
	 *
	 * Some relationship types need special treatment on the view so, we need an "extra_html" field
	 * In this case, for the "IsPreviousVersionOf" relationship we ask Yii to rendre a partial view to generate
	 * the special  HTML that will go into the "extra_html" field
	 * Also we need to remove internal ids, as we want to use the public DOI and full DOI only on the view
	 *
	 * @param string optionally pass the list of types of relations to retrieve, otherwise retrieve them all
	 * @return array of string representing the  list of relations ready to be displayed
	*/
	public function getRelations(string $relationship_type = null): array
	{
		$formattedRelations = [];
		$relations = $this->_cachedDatasetConnections->getRelations();
		foreach ($relations as $relation) {
			$formattedRelation = $relation;
			//those two are internal ids, no need to be exposd in view
			unset($formattedRelation['dataset_id']);
			unset($formattedRelation['related_id']);
			//we need full DOI that include prefix too
			$formattedRelation['full_dataset_doi'] = Yii::app()->params['mds_prefix']."/". $relation['dataset_doi'];
			$formattedRelation['full_related_doi'] = Yii::app()->params['mds_prefix']."/". $relation['related_doi'];

			if ( "IsPreviousVersionOf" == $formattedRelation['relationship']) {
				$formattedRelation['extra_html'] = $this->_controller->renderFile(
														 Yii::getPathOfAlias("application")."/views/dataset/_connection_IsPreviousVersionOf.php",
														array("relation" => $formattedRelation),
														true // this tell Yii to return the rendered view instead of displaying it
													);
			}
			else {
				$formattedRelation['extra_html'] = "";
			}
			array_push($formattedRelations, $formattedRelation);
		}
		return $formattedRelations;
	}

	/**
	 * retrieval of keywords
	 *
	 * @todo
	 * @return array of string representing the dataset headline attributes
	*/
	public function getKeywords(): array
	{
		return [];
	}

}
?>