<?php

declare(strict_types=1);

namespace app\components;

use GigaDB\models\Dataset;
use GigaDB\models\File;
use GigaDB\services\URLsService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Yii;
use yii\base\Component;

/**
 * DatasetFilesUpdater
 *
 * encapsulate business logic for updating the file table
 */
final class DatasetFilesUpdater extends Component
{
    /**
     * @var string Dataset identifier for the dataset whose files need to be operated on
     */
    public string $doi;
    /**
     * @var URLsService URLs helper functions (here we interested in batch grab of specific response header)
     */
    public URLsService $us;
    /**
     * @var \GuzzleHttp\Client web client needed for URLsService
     */
    public \GuzzleHttp\Client $webClient;

    /**
     * Updates file sizes for all files listed in fileSizes.tsv fetched from FTP
     * server
     *
     * @return int returns the number of files that has been successfully updated
     */
    public function updateFileSizes($doi): int
    {
        $success = 0;
        
        $ftpHost = Yii::$app->params['DB_BACKUP_HOST'];
        $ftpUrl = 'ftp://' . $ftpHost . '/datasets/' . $doi . ".tsv";
        $fileSizesContent = "";
        
//        # Fetch $doi.tsv
//        $webClient = new \GuzzleHttp\Client();
//        $response = $webClient->request('GET', $ftpUrl);
//        if ($response->getStatusCode() === 200) {
//            $fileSizesContent = $response->getBody()->getContents();
//        } else {
//            throw new BadResponseException("Error downloading file: status code " . $response->getStatusCode());
//        }
//
//        # Update file sizes in database
//        $fileSizes = explode("\n", $fileSizesContent);
//        for($i = 0; $i < count($fileSizes); ++$i) {
//            $tokens = explode("\t", $fileSizes[$i]);
//            $fileSize = $tokens[0];
//            $fileName = $tokens[1];
//
//            $location = "$fileName";
//            $f = File::find()->where(["location" => $location])->one();
//            $f->size = $fileSize;
//            if ($f->save()) {
//                $success++;
//            }
//        }

        return $success;
    }

    /**
     * Method to update the file size for the all the files of the dataset identified with $doi
     *
     * @return int returns the number of files that has been successfully updated
     */
    public function updateFileSize(): int
    {
        $success = 0;
        $zeroOutRedirectsAndDirectories = function ($response, $url) {
            if (301 === $response->getStatusCode() || str_ends_with($url, "/")) {
                return 0;
            }
            return null;
        };


        $d = Dataset::find()->where(["identifier" => $this->doi])->one();

        $urls =  File::find()
                ->select(["location"])
                ->where(["dataset_id" => $d->id])
                ->asArray(true)
                ->all();
        $values = function ($item) {
            return $item["location"];
        };
        $flatURLs = array_map($values, $urls);
        $this->us->urls = $flatURLs;
        $contentLengthList = $this->us->fetchResponseHeader(
            "Content-Length",
            $this->webClient,
            $zeroOutRedirectsAndDirectories
        );
        foreach ($contentLengthList as $location => $contentLength) {
            $f = File::find()->where(["location" => $location])->one();
            $f->size = (int) $contentLength;
            if ($f->save()) {
                $success++;
            }
        }

        return $success;
    }
}
