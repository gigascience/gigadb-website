<?php

declare(strict_types=1);

class m250219_001214_update_relationship_table_schema_and_insert_data extends CDbMigration
{
    public function safeUp()
    {
        $this->addColumn('relationship', 'reciprocal_name', 'VARCHAR(255) DEFAULT NULL');

        $this->update('relationship', ['reciprocal_name' => 'IsCitedBy'], "name = 'Cites'");
        $this->update('relationship', ['reciprocal_name' => 'Cites'], "name = 'IsCitedBy'");

        $this->update('relationship', ['reciprocal_name' => 'IsSupplementTo'], "name = 'IsSupplementedBy'");
        $this->update('relationship', ['reciprocal_name' => 'IsSupplementedBy'], "name = 'IsSupplementTo'");

        $this->update('relationship', ['reciprocal_name' => 'IsContinuedBy'], "name = 'Continues'");
        $this->update('relationship', ['reciprocal_name' => 'Continues'], "name = 'IsContinuedBy'");

        $this->update('relationship', ['reciprocal_name' => 'Describes'], "name = 'IsDescribedBy'");
        $this->update('relationship', ['reciprocal_name' => 'IsDescribedBy'], "name = 'Describes'");

        $this->update('relationship', ['reciprocal_name' => 'HasMetadata'], "name = 'isMetadataFor'");
        $this->update('relationship', ['reciprocal_name' => 'isMetadataFor'], "name = 'HasMetadata'");

        $this->update('relationship', ['reciprocal_name' => 'HasVersion'], "name = 'IsVersionOf'");
        $this->update('relationship', ['reciprocal_name' => 'IsVersionOf'], "name = 'HasVersion'");

        $this->update('relationship', ['reciprocal_name' => 'IsNewVersionOf'], "name = 'IsPreviousVersionOf'");
        $this->update('relationship', ['reciprocal_name' => 'IsPreviousVersionOf'], "name = 'IsNewVersionOf'");

        $this->update('relationship', ['reciprocal_name' => 'IsPartOf'], "name = 'HasPart'");
        $this->update('relationship', ['reciprocal_name' => 'HasPart'], "name = 'IsPartOf'");

        $this->update('relationship', ['reciprocal_name' => 'IsReferencedBy'], "name = 'References'");
        $this->update('relationship', ['reciprocal_name' => 'References'], "name = 'IsReferencedBy'");

        $this->update('relationship', ['reciprocal_name' => 'IsDocumentedBy'], "name = 'Documents'");
        $this->update('relationship', ['reciprocal_name' => 'Documents'], "name = 'IsDocumentedBy'");

        $this->update('relationship', ['reciprocal_name' => 'IsCompiledBy'], "name = 'Compiles'");
        $this->update('relationship', ['reciprocal_name' => 'Compiles'], "name = 'IsCompiledBy'");

        $this->update('relationship', ['reciprocal_name' => 'IsReviewedBy'], "name = 'Reviews'");
        $this->update('relationship', ['reciprocal_name' => 'Reviews'], "name = 'IsReviewedBy'");

        $this->update('relationship', ['reciprocal_name' => 'IsRequiredBy'], "name = 'Requires'");
        $this->update('relationship', ['reciprocal_name' => 'Requires'], "name = 'IsRequiredBy'");

        $this->update('relationship', ['reciprocal_name' => 'IsObsoletedBy'], "name = 'Obsoletes'");
        $this->update('relationship', ['reciprocal_name' => 'Obsoletes'], "name = 'IsObsoletedBy'");

        $this->update('relationship', ['reciprocal_name' => 'IsCollectedBy'], "name = 'Collects'");
        $this->update('relationship', ['reciprocal_name' => 'Collects'], "name = 'IsCollectedBy'");

        $this->update('relationship', ['reciprocal_name' => 'IsVariantFormOf'], "name = 'IsOriginalFormOf'");
        $this->update('relationship', ['reciprocal_name' => 'IsOriginalFormOf'], "name = 'IsVariantFormOf'");

        $this->update('relationship', ['reciprocal_name' => 'IsIdenticalTo'], "name = 'IsIdenticalTo'");
        $this->update('relationship', ['reciprocal_name' => 'IsIdenticalTo'], "name = 'IsIdenticalTo'");

        $this->update('relationship', ['reciprocal_name' => 'IsDerivedFrom'], "name = 'IsSourceOf'");
        $this->update('relationship', ['reciprocal_name' => 'IsSourceOf'], "name = 'IsDerivedFrom'");

        $this->update('relationship', ['reciprocal_name' => 'IsPublishedIn'], "name = 'IsPublishedIn'");
        $this->update('relationship', ['reciprocal_name' => 'IsPublishedIn'], "name = 'IsPublishedIn'");
    }

    public function safeDown()
    {
        $this->dropColumn('orders', 'reciprocal_name');
    }
}
