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
        
        # Create URL to download $doi.md5 file, e.g. https://ftp.cngb.org/pub/gigadb/pub/10.5524/102001_103000/102236/102236.md5
        // $url = "https://ftp.cngb.org/pub/gigadb/pub/10.5524/102001_103000/$doi/$doi.md5";
        $url = "./tests/_data/$doi.md5";
        echo $url.PHP_EOL;

        // Check $doi.md5 file exists
        $file_exists = @fopen($url, 'r');
        if($file_exists) {
            # Download and parse
            $contents = file_get_contents($url);
            $lines = explode("\n", $contents);
            $file_md5_values = array();
            foreach ($lines as $line) {
                $tokens = explode("  ", $line);
                $file_md5_values += array($tokens[1] => $tokens[0]);
            }
            print_r($file_md5_values);
            # Update file_attributes table with md5 checksum value
            //$dataset = Dataset::model()-> find('$identifier=:identifier', array(':identifier'=>$doi));
            $file = File::model()->findByAttributes(array(
                'dataset_id' => "8",
                'name' => 'Pygoscelis_adeliae.scaf.fa.gz',
            ));
            print_r($file->location.PHP_EOL);
            print_r($file->id);
            $fa = FileAttributes::model()->findByAttributes(array(
                'file_id' => $file->id,
                'attribute_id' => "605",
            ));
            $fa->value = "888" ;
            $fa->save();
        }
        else {
            echo 'File does not exist';
        }
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