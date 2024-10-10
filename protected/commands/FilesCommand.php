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

    /** @const int  S3_BUCKET_GIGADB_DATASETS_METADATA_BUCKET_URL Domain URL for S3 bucket containing md5 files */
    const S3_BUCKET_GIGADB_DATASETS_METADATA_BUCKET_URL = 'https://s3.ap-northeast-1.amazonaws.com/gigadb-datasets-metadata';

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

            # Download and parse dataset md5 file
            $url = $this->findDatasetMd5FileUrl($dataset);
            $contents = DownloadService::downloadFile($url);
            $lines = explode("\n", $contents);
            foreach ($lines as $line) {
                # Last line in $doi.md5 file might be empty
                if(!str_contains($line, '  ')) {
                    break;
                }
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
        catch (\GuzzleHttp\Exception\GuzzleException $ge) {
            echo $ge->getMessage();
        }
    }

    /**
     * Returns the URL for a dataset's md5 file
     *
     * First checks if md5 file can be found in S3 bucket. If not, then checks
     * whether the URL of the md5 file could be:
     * https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/100006.md5
     * https://ftp.cngb.org/pub/gigadb/pub/10.5524/101001_102000/101001/101001.md5
     * https://ftp.cngb.org/pub/gigadb/pub/10.5524/102001_103000/102236/102236.md5
     *
     * @param $dataset
     * @return string
     * @throws Exception
     */
    private function findDatasetMd5FileUrl($dataset): string
    {
        $doi = $dataset->identifier;
        
        # Test if doi.md5 exists in S3 bucket first
        $bucketMd5Url = self::S3_BUCKET_GIGADB_DATASETS_METADATA_BUCKET_URL . "/$doi.md5";
        if(DownloadService::fileExists($bucketMd5Url)) {
            return $bucketMd5Url;
        }
        else {
            foreach ($dataset::RANGES as $range) {
                $url = Yii::app()->params['ftp_connection_url'] . "/pub/gigadb/pub/10.5524/$range/$doi/$doi.md5";
                // Check URL resolves to a real file
                echo "Processing $url" . PHP_EOL;
                $file_exists = DownloadService::fileExists($url);
                if ($file_exists)
                    return $url;
            }
        }
        throw new Exception("No $doi.md5 file could be found for dataset DOI $doi");
    }


}