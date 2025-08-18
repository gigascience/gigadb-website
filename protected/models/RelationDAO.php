<?php

/**
 * Will save to the database the relation created between two datasets
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class RelationDAO
{
	/**
	 * It sets up and save a supplied relation object to reciprocate a given persisted relation
	 *
	 * @param Relation $relating_rel the relation for which to create a reciprocal relation
	 * @param Relation $reciprocal_rel the new reciprocating relation to be
	 **/
	public function createReciprocalTo(Relation $relating_rel, Relation $reciprocal_rel)
	{
		$dataset_id = Dataset::model()->findByAttributes(array('identifier' => $relating_rel->getRelatedDOI()))->id ;
		$related_doi = Dataset::model()->findByAttributes(array('id' => $relating_rel->getDatasetID()))->identifier ;
		$reciprocal_rel->setDatasetID($dataset_id);
		$reciprocal_rel->setRelatedDOI($related_doi);
		$reciprocal_rel->setRelationship($relating_rel->getRelationship()->getReciprocalName());

		if (!$reciprocal_rel->save()) {
			throw new CException('Failed as it was unable to save the reciprocal relation');
		}
	}
}

?>
