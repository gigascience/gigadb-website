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
        $helpText = "checks files url for a specific dataset in the database" . PHP_EOL;
        $helpText .= "Usage: ./protected/yiic files checkUrls --doi=<DOI>" . PHP_EOL;

        return $helpText;
    }

    /**
     * Update MD5 checksum in file_attributes table
     * 
     * @param $doi
     * @return string
     * @throws CException
     */
    public function actionUpdateMD5FileAttribute($doi) {
        echo "Executing FilesCommand::actionUpdateMD5ChecksumFileAttribute with $doi".PHP_EOL;
        
        # Create URL to download $doi.md5 file, e.g. https://ftp.cngb.org/pub/gigadb/pub/10.5524/102001_103000/102236/readme_102236.txt
        $url = "https://ftp.cngb.org/pub/gigadb/pub/10.5524/102001_103000/$doi/readme_$doi.txt";
        echo $url.PHP_EOL;

        // Open file
        $check = @fopen($url, 'r');
        // Check file exists
        if(!$check){
            echo 'File does not exist';
        }else{
            echo 'File exists';
            # Download $doi.md5
            $contents = file_get_contents($url);
            echo $contents;
        }

        # Parse $doi.md5 file using spreadsheet parser
        # Update file_attributes table with md5 checksum value
    }

    private function updateFileAttribute($data) {
        
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