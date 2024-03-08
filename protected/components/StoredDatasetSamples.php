<?php

/**
 * DAO class to retrieve the samples associated to a dataset
 *
 *
 * @param int $id of the dataset for which to retrieve the information
 * @param CDbConnection $dbConnection The database connection object to interact with the database storage
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetSamples extends DatasetComponents implements DatasetSamplesInterface
{
    private int $_id;
    private CDbConnection $_db;

    public function __construct(int $dataset_id, CDbConnection $dbConnection)
    {
        parent::__construct();
        $this->_id = $dataset_id;
        $this->_db = $dbConnection;
    }

    /**
     * return the dataset id
     *
     * @return int
     */
    public function getDatasetId(): int
    {
        return $this->_id;
    }

    /**
     * return the dataset identifier (DOI)
     *
     * @return string
     */
    public function getDatasetDOI(): string
    {
        return $this->getDOIfromId($this->_db, $this->_id);
    }

    /**
     * retrieve, cache and format the samples associated to a dataset
     *
     * @return array of files array maps
     */
    public function getDatasetSamples(?string $limit = "ALL", ?int $offset = 0): array
    {

        $objectToHash =  function ($sample) {

            $toNameValueHash = function ($sample_attribute) {
                return array( $sample_attribute->attribute->attribute_name => $sample_attribute->value);
            };

            return array(
                'sample_id' => $sample->id,
                'linkName' => $sample->getLinkName(),
                'dataset_id' => $this->_id,
                'species_id' => $sample->species_id,
                'tax_id' => $sample->species->tax_id,
                'common_name' => $sample->species->common_name,
                'scientific_name' => $sample->species->scientific_name,
                'genbank_name' => $sample->species->genbank_name,
                'name' => $sample->name,
                'consent_document' => $sample->consent_document,
                'submitted_id' => $sample->submitted_id,
                'submission_date' => $sample->submission_date,
                'contact_author_name' => $sample->contact_author_name,
                'contact_author_email' => $sample->contact_author_email,
                'sampling_protocol' => $sample->sampling_protocol,
                'sample_attributes' => array_map($toNameValueHash, SampleAttribute::model()->findAllByAttributes(array('sample_id' => $sample->id))),
            );
        };
        $sql = "select
		ds.sample_id as id, s.name, s.species_id, s.consent_document, s.submitted_id, s.submission_date, s.contact_author_name, s.contact_author_email, s.sampling_protocol
		from sample s, dataset_sample ds
		where ds.sample_id = s.id and ds.dataset_id=:id limit $limit offset $offset" ;
        //In the sql above, make sure that the only 'id' field is ds.sample_id, otherwise ActiveRecord may pick up the wrong id field (e.g: ds.id)
        $samples = Sample::model()->findAllBySql($sql, array('id' => $this->_id));
        $result = array_map($objectToHash, $samples);
        return $result;
    }

    /**
     * count number of samples associated to a dataset
     *
     * @return int how many samples are associated with the dataset
     */
    public function countDatasetSamples(): int
    {
        $criteria=new CDbCriteria;
        $criteria->join="LEFT join dataset on dataset.id = dataset_id";
        $criteria->condition='dataset.identifier=:identifier';
        $criteria->params=array(':identifier'=> $this->getDatasetDOI());

        return  DatasetSample::model()->count($criteria);

    }

}
