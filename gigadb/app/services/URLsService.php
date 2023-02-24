<?php

declare(strict_types=1);

namespace GigaDB\services;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\base\Component;

/**
 * Service that provide generic operation related to URLs. batch mode by default
 */
final class URLsService extends Component
{
    /**
     * @param array $urls a property for url(s) to operate on, immutable
     * @param array $config
     */
    public function __construct(readonly array $urls, array $config = [])
    {
         parent::__construct($config);
    }

    /**
     * Retrieve a specific header from the URLs' response
     * it's possible to pass a filtering function to alter the value in specific situation
     * if the filter returns null, the actual value is returned
     *
     * @param string $headerLabel the header whose value we want for all urls
     * @param ClientInterface $webClient a web client to perform the HTTP request with
     * @param callable|null $filter function to filter out specific value, need to be passed $response and $url
     * @return array
     */
    public function fetchResponseHeader(string $headerLabel, ClientInterface $webClient, ?callable $filter): array
    {
        $responses = [];
        foreach ($this->urls as $url) {
            try {
                $response = $webClient->head($url);
            } catch (GuzzleException $e) {
                Yii::error($e->getTraceAsString());
                continue;
            }
            $headerValue = $filter($response, $url) ?? $response->getHeaderLine($headerLabel);
            $responses[$url] = $headerValue;
        }
        return $responses;
    }
}
