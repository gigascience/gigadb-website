<?php

namespace GigaDB\services;

use CException;
use DownloadService;
use Yii;
use yii\base\BaseObject;
use yii\base\Component;

/**
 * Services that provide generic operation on the file table
 */
class DatasetFileService extends Component
{
    /**
     * @property string
     */
    private string $doi;

    /**
     * @param string $doi The service needs a DOI on which to operate
     * @param $config in case we wan to pass extra config to aprent Yii\BaseObject
     */
    public function __construct(string $doi, $config = [])
    {
        parent::__construct($config);
        $this->doi = $doi;
    }

    /**
     *
     * Query database for files url associated to dataset passed as parameter and check that they resolve ok
     * otherwise output the url
     *
     * @return void
     * @throws CException
     */
    public function checkFilesUrl(): void
    {
        $doi = $this->doi;
        $sql = <<<END
SELECT f.location
FROM file f, dataset d
WHERE f.dataset_id = d.id and d.identifier = '$doi';
END;

        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($rows as $row) {
            $parts = parse_url($row['location']);
            $pathComponents = pathinfo($parts['path']);

            if ("ftp" === $parts['scheme']) {
                echo $row['location'] . PHP_EOL;
                continue;
            }
            if (!$pathComponents['extension']) {
                echo $row['location'] . PHP_EOL;
                continue;
            }

            $file_exists = DownloadService::fileExists($row['location']);
            if (!$file_exists)
                echo $row['location'] . PHP_EOL;
        }
    }
}

