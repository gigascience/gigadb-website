<?php

/**
 * DAO class to retrieve the files associated to a dataset
 *
 *
 * @param int $id of the dataset for which to retrieve the information
 * @param CDbConnection $dbConnection The database connection object to interact with the database storage
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetFiles extends DatasetComponents implements DatasetFilesInterface
{
    private const FILES_ROWS_LIMIT = 8000;
    private $_id;
    private $_db;

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
     * retrieve, cache and format the files associated to a dataset
     *
     * @return array of files array maps
     */
    public function getDatasetFiles(?string $limit = "ALL", ?int $offset = 0): array
    {
        $objectToHash =  function ($file) {

            $toNameValueHash = function ($file_attribute) {
                return array( $file_attribute->attribute->attribute_name => $file_attribute->value);
            };

            return array(
                'id' => $file->id,
                'dataset_id' => $file->dataset_id,
                'name' => $file->name,
                'location' => $file->location,
                'extension' => $file->extension,
                'size' => $file->size,
                'description' => $file->description,
                'date_stamp' => $file->date_stamp,
                'format' => $file->format->name,
                'type' => $file->type->name,
                'file_attributes' => array_map($toNameValueHash, $file->fileAttributes),
                'download_count' => $file->download_count,
            );
        };
        $sql = "select
		id, dataset_id, name, location, extension, size, description, date_stamp, format_id, type_id, download_count
		from file
		where dataset_id=:id limit $limit offset $offset" ;
        ;
        $files = File::model()->findAllBySql($sql, array('id' => $this->_id));
        $result = array_map($objectToHash, $files);
        // var_dump($result);
        return $result;
    }

    /**
     * retrieve the sample information attached to the files associated to a dataset
     *
     * @return array of files array maps
     */
    public function getDatasetFilesSamples(): array
    {
        // 'sample_id' => 1,
        // 'sample_name' => "Sample 1",
        // 'file_id' => 1,

        $sql = "select s.id as sample_id, s.name as sample_name, f.id as file_id
		from sample s, file_sample fs, file f
		where s.id = fs.sample_id and f.id = fs.file_id
		and f.dataset_id=:id";
        $command = $this->_db->createCommand($sql);
        $command->bindParam(":id", $this->_id, PDO::PARAM_INT);
        $result = $command->queryAll();
        return $result;
    }
}
