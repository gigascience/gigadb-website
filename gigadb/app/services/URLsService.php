<?php

declare(strict_types=1);

namespace GigaDB\services;

use CException;
use DownloadService;
use Yii;
use yii\base\BaseObject;
use yii\base\Component;

/**
 * Service that provide generic operation related to URLs. batch mode by default
 */
final class URLsService extends Component
{
    /**
     * @param array $urls url(s) to operate on
     * @param array $config
     */
    public function __construct(readonly array $urls, array $config = [])
    {
         parent::__construct($config);
    }

    public function fetchResponseHeader(string $string): array
    {
        return [];
    }

}
