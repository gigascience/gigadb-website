<?php

declare(strict_types=1);

namespace app\components;

use GigaDB\models\Dataset;
use GigaDB\models\File;
use GigaDB\services\URLsService;
use GuzzleHttp\Client;
use Yii;
use yii\base\Component;


final class DatasetFilesUpdater extends Component
{
    public string $doi;

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
        $flatURLs = array_map($values,$urls);
        $webClient = new Client([ 'allow_redirects' => false ]);
        $us = new URLsService($flatURLs);
        $contentLengthList = $us->fetchResponseHeader("Content-Length", $webClient, $zeroOutRedirectsAndDirectories);
        foreach($contentLengthList as $location => $contentLength){
            $f = File::find()->where(["location" => $location])->one();
            $f->size = (int) $contentLength;
            if ($f->save())
                $success++;
        }

        return $success;
    }

}