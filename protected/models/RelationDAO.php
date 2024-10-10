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
     const RECIPROCAL_RELATION = [
		 "IsCitedBy"           => "Cites",
		 "Cites"               => "IsCitedBy",
         "IsSupplementTo"      => "IsSupplementedBy",
         "IsSupplementedBy"    => "IsSupplementTo",
		 "IsContinuedBy"       => "Continues",
		 "Continues"           => "IsContinuedBy",
		 "Describes"           => "IsDescribedBy",
		 "IsDescribedBy"       => "Describes",
		 "HasMetadata"         => "isMetadataFor",
		 "IsMetadataFor"       => "HasMetadata",
		 "HasVersion"          => "IsVersionOf",
		 "IsVersionOf"         => "HasVersion",
         "IsNewVersionOf"      => "IsPreviousVersionOf",
         "IsPreviousVersionOf" => "IsNewVersionOf",
         "IsPartOf"            => "HasPart",
         "HasPart"             => "IsPartOf",
         "IsReferencedBy"      => "References",
         "References"          => "IsReferencedBy",
		 "IsDocumentedBy"      => "Documents",
		 "Documents"           => "IsDocumentedBy",
		 "IsCompiledBy"        => "Compiles",
		 "Compiles"            => "IsCompiledBy",
		 "IsReviewedBy"        => "Reviews",
		 "Reviews"             => "IsReviewedBy",
		 "IsRequiredBy"        => "Requires",
		 "Requires"            => "IsRequiredBy",
		 "IsObsoletedBy"       => "Obsoletes",
		 "Obsoletes"           => "IsObsoletedBy",
		 "IsCollectedBy"       => "Collects",
		 "Collects"            => "IsCollectedBy",
		 "IsVariantFormOf"     => "IsOriginalFormOf",
		 "IsOriginalFormOf"    => "IsVariantFormOf",
		 "IsIdenticalTo"       => "IsIdenticalTo",
		 "IsDerivedFrom"       => "IsSourceOf",
		 "IsSourceOf"          => "IsDerivedFrom"
     ];

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
        $reciprocal_rel->setRelationship(self::RECIPROCAL_RELATION[$relating_rel->getRelationship()->getName()]);

		if (!$reciprocal_rel->save()) {
			throw new CException('Failed as it was unable to save the reciprocal relation');
		}
	}
}

?>
