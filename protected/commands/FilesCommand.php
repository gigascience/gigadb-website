<?php
/**
 * Command to check fils url
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */


class FilesCommand extends CConsoleCommand
{

    public function getHelp()
    {
        $helpText = "checks files url for a specific dataset in the database" . PHP_EOL;
        $helpText .= "Usage: ./protected/yiic files checkUrls --doi=<DOI>" . PHP_EOL;

        return $helpText;
    }

    /**
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
                if ( "ftp" === $parts['scheme']) {
                    echo $row['location'].PHP_EOL;
                    continue;
                }
                $headers = get_headers($row['location'], 1, stream_context_create(array('http' => array('method' => 'HEAD'))));
                if ($headers[0] !== 200)
                    echo $row['location'].PHP_EOL;

            }

        } catch (CDbException $e) {
            Yii::log($e->getMessage(),"error");
            return 1;
        }


    }



}