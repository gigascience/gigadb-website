<?php

interface DatasetFilesInterface {

    /**
     * @return return a dataset identifier (the document related part of a DOI)
     */
    public function getDatasetIdentifier() ;

    /**
     * @return return an url (remote location url - currently on ftp - of a file associated to a dataset)
     */
    public function getLocationUrl() ;
}
 ?>
