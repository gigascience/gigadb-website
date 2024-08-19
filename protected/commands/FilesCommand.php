<?php
/**
 * Command to check fils url
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @author Peter Li <peter+git@gigasciencejournal.com>
 * @license GPL-3.0
 */

use \yii\console\ExitCode;
use \GigaDB\services\DatasetFileService;

class FilesCommand extends CConsoleCommand
{
    /** @const int RETURN_ASSOCIATIVE_ARRAY set to 1 it is passed to get_headers() second parameters so output is a list of key/value pairs */
    const RETURN_ASSOCIATIVE_ARRAY = 1 ;

    /** @const int  HTTP_STATUS_OK HTTP status code from HTTP response indicating successful GET */
    const HTTP_STATUS_OK = 200 ;

    /** @const string  GIGADB_METADATA_DIR Path in bastion server where doi.md5 file can be found */
    const GIGADB_METADATA_DIR = '/var/share/gigadb/metadata/';

    /**
     * @return string
     */
    public function getHelp()
    {
        $helpText = "Checks files url for a specific dataset in the database" . PHP_EOL;
        $helpText .= "Usage: ./protected/yiic files checkUrls --doi=<DOI>" . PHP_EOL;
        $helpText .= "Updates md5 checksum attribute value for all files in a given dataset" . PHP_EOL;
        $helpText .= "Usage: ./protected/yiic files updateMD5FileAttributes --doi=<DOI>" . PHP_EOL;

        return $helpText;
    }

    /**
     * Update MD5 checksum attribute for all files in a dataset given its DOI
     * 
     * @param $doi
     * @return void
     */
    public function actionUpdateMD5FileAttributes($doi) {
        try {
            // Dataset id is required for querying files
            $dataset = Dataset::model()->findByAttributes(array(
                'identifier' => $doi,
            ));
            if(is_null($dataset))
                throw new Exception("No dataset found in database with DOI $doi");

            # Fetch and parse dataset md5 file
            $md5FilePath = $this->findDatasetMd5FilePath($doi);
            $contents = file_get_contents($md5FilePath);
            $lines = explode("\n", $contents);
            foreach ($lines as $line) {
                # Last line in $doi.md5 file might be empty
                if(!str_contains($line, '  ')) {
                    break;
                }
                # md5 value and file name is separated by 2 spaces in doi.md5 file
                $tokens = explode("  ", $line);
                // Only parse lines with content in md5 file
                if($tokens[0] !== "") {
                    $md5_value = $tokens[0];
                    # Make use of whole file path
                    $filepath = ltrim($tokens[1], './');
                    if ($filepath === "$doi.md5")  // Ignore $doi.md5 file
                        continue;

                    # Find file with unique URL location that ends with filepath
                    $criteria = new CDbCriteria();
                    $criteria->addColumnCondition(['dataset_id' => $dataset->id]);
                    $criteria->addSearchCondition('location', "%$filepath", false);
                    $file = File::model()->find($criteria);
                    $file->updateMd5Checksum($md5_value);
                }
            }
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Returns the path for a dataset's md5 file
     *
     * @param $dataset
     * @return string
     * @throws Exception
     */
    private function findDatasetMd5FilePath($doi): string
    {
        # Test if doi.md5 exists
        $bucketMd5Path = self::GIGADB_METADATA_DIR . "/$doi.md5";
        if(file_exists($bucketMd5Path)) {
            return $bucketMd5Path;
        }
        throw new Exception("No $doi.md5 file could be found for dataset DOI $doi");
    }


}