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

		$reciprocal_rel->setDatasetID( $dataset_id );

		$reciprocal_rel->setRelatedDOI( $related_doi );

		if ($relating_rel->getRelationship()=="IsSupplementTo") {

			$reciprocal_rel->setRelationship('IsSupplementedBy');
		}

		elseif ($relating_rel->getRelationship()=="IsSupplementedBy") {

			$reciprocal_rel->setRelationship('IsSupplementTo');
		}
		elseif ($relating_rel->getRelationship()=="IsNewVersionOf") {

			$reciprocal_rel->setRelationship('IsPreviousVersionOf');
		}
		elseif ($relating_rel->getRelationship()=="IsPreviousVersionOf") {

			$reciprocal_rel->setRelationship('IsNewVersionOf');
		}
		elseif ($relating_rel->getRelationship()=="IsPartOf") {

			$reciprocal_rel->setRelationship('HasPart');
		}
		elseif ($relating_rel->getRelationship()=="HasPart") {

			$reciprocal_rel->setRelationship('IsPartOf');
		}
		elseif ($relating_rel->getRelationship()=="IsReferencedBy") {

			$reciprocal_rel->setRelationship('References');
		}
		elseif ($relating_rel->getRelationship()=="References") {

			$reciprocal_rel->setRelationship('IsReferencedBy');
		}

		$reciprocal_rel->save();
	}

}

?>