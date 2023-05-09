<?php

declare(strict_types=1);

namespace app\components;

use GigaDB\models\Dataset;
use GigaDB\models\File;
use GuzzleHttp\Client;
use GigaDB\services\URLsService;
use Yii;
use yii\base\Component;
use yii\console\Exception;

/**
 * FilesURLsFetcher
 *
 * encapsulate business logic for checking validity of the file urls
 */
final class FilesURLsFetcher extends Component
{
    const TIMEOUT = 10;
    /**
     * @var string Dataset identifier for the dataset whose files need to be operated on
     */
    public string $doi;

    /**
     * @var \GuzzleHttp\Client web client needed for URLsService
     */
    public \GuzzleHttp\Client $webClient;

    public function verifyURLs(): array
    {
        $d = Dataset::find()->where(["identifier" => $this->doi])->one();
        if (null === $d) {
            throw new Exception("DOI does not exist");
        }

        $urls =  File::find()
            ->select(["location"])
            ->where(["dataset_id" => $d->id])
            ->asArray(true)
            ->all();
        $values = function ($item) {
            return $item["location"];
        };
        $us = new URLsService(["urls" => array_map($values, $urls) ]);
        return $us->checkUrls($this->webClient);
    }
}
