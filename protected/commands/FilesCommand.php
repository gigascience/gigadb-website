<?php
/**
 * Command to check fils url
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */

use \yii\console\ExitCode;

class FilesCommand extends CConsoleCommand
{
    /** @const int RETURN_ASSOCIATIVE_ARRAY set to 1 it is passed to get_headers() second parameters so output is a list of key/value pairs */
    const RETURN_ASSOCIATIVE_ARRAY = 1 ;

    /** @const int  HTTP_STATUS_OK HTTP status code from HTTP response indicating successful GET */
    const HTTP_STATUS_OK = 200 ;

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
     */
    public function actionUpdateMD5FileAttributes($doi) {
        Yii::import('application.controllers.*');
        $adminFileController = new AdminFileController('afc');
        
        try {
            $url = $this->findDatasetMd5FileUrl($doi);
            echo "Processing $url".PHP_EOL;

            // Dataset id is required for querying files
            $dataset = Dataset::model()->findByAttributes(array(
                'identifier' => $doi,
            ));

            # Download and parse dataset md5 file
            $contents = file_get_contents($url);
            $lines = explode("\n", $contents);
            foreach ($lines as $line) {
                $tokens = explode("  ", $line);
                $md5 = $tokens[0];
                $filename = basename($tokens[1]);
                if ($filename === "$doi.md5")  // Ignore $doi.md5 file
                    continue;

                # Update file_attributes table with md5 checksum value
                $file = File::model()->findByAttributes(array(
                    'dataset_id' => $dataset->id,
                    'name' => $filename,
                ));
                $adminFileController->updateMd5Checksum($file->id, $md5);
            }
        }
        catch (Exception $e) {
            Yii::log($e->getMessage(), "error");
        }
    }

    /**
     * Returns the URL for a dataset's md5 file
     * 
     * Determines the URL of the md5 file as it could be:
     * https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/100006.md5
     * https://ftp.cngb.org/pub/gigadb/pub/10.5524/101001_102000/101001/101001.md5
     * https://ftp.cngb.org/pub/gigadb/pub/10.5524/102001_103000/102236/102236.md5
     *
     * @param $doi
     * @return string
     * @throws ErrorException
     */
    private function findDatasetMd5FileUrl($doi): string
    {
        // Directory names representing ranges of dataset DOIs
        $ranges = ['104001_105000', '103001_104000', '102001_103000', '101001_102000', '100001_101000'];

        foreach ($ranges as $range) {
            $url = "https://ftp.cngb.org/pub/gigadb/pub/10.5524/$range/$doi/$doi.md5";
            $file_exists = @fopen($url, 'r');
            if ($file_exists)
                return $url;
        }

        throw new ErrorException("$doi.md5 file not found for dataset DOI $doi");
    }

    /**
     *
     * Query databse for files url associated to dataset passed as parameter and check that they resolve ok
     * otherwise output the url
     *
     * @param $doi string identifier of the dataset for which to check url
     * @return int
     * @throws CException
     */
    public function actionCheckUrls($doi) {
        $sql =<<<END
SELECT f.location
FROM file f, dataset d
WHERE f.dataset_id = d.id and d.identifier = '$doi';
END;

        try {
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($rows as $row) {
                $parts = parse_url($row['location']);
                $pathComponents = pathinfo($parts['path']);

                if( "ftp" === $parts['scheme']) {
                    echo $row['location'].PHP_EOL;
                    continue;
                }
                if( !$pathComponents['extension'] ) {
                    echo $row['location'].PHP_EOL;
                    continue;
                }
                $headers = get_headers($row['location'], self::RETURN_ASSOCIATIVE_ARRAY, stream_context_create([
                    'http' => [
                        'method' => 'HEAD'
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]));
                if(!CompatibilityHelper::str_contains($headers[0],self::HTTP_STATUS_OK))
                    echo $row['location'].PHP_EOL;
            }

        } catch (CDbException $e) {
            Yii::log($e->getMessage(),"error");
            return ExitCode::IOERR;
        }

        return ExitCode::OK;

    }



}