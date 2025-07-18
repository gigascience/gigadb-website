<?php

declare(strict_types=1);

namespace GigaDB\services;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Exception\ConnectException;
use Yii;
use yii\base\Component;

/**
 * Service that provide generic operation related to URLs.
 * Expects the list of URLs to operate on as to be an argument to this class constructor
 */
final class URLsService extends Component
{
    const TIMEOUT = 10;
    public array $urls;

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

    /**
     * Method to check the validity for the all the urls
     *
     * @return array returns the list of urls with problems
     * @throws Exception|GuzzleException
     */
    public function checkURLs(ClientInterface $webClient): array
    {

        $badUrls = [];
        foreach ($this->urls as $url) {
            $parts = parse_url($url);
            $scheme = $parts['scheme'];
            if ("ftp" === $scheme) {
                $badUrls[$url] = "Wrong scheme (ftp://)";
                continue;
            }
            if (str_ends_with($url, "/")) {
                $badUrls[$url] = "URL appears to be a directory (/)";
                continue;
            }

            try {
                $response = $webClient->head($url, ['timeout' => self::TIMEOUT]);
                if (301 === $response->getStatusCode()) {
                    $badUrls[$url] = "URL appears to be a directory listing";
                    continue;
                }
            } catch (ClientException $e) {
                $badUrls[$url] = "Resource cannot be downloaded, not found or forbidden (4xx)";
                continue;
            } catch (ServerException $e) {
                $badUrls[$url] = "Resource cannot be downloaded, remote endpoint crashed (5xx)";
                continue;
            } catch (ConnectException $e) {
                $badUrls[$url] = "Request time out, because of a network error ";
                continue;
            }
        }

        return $badUrls;
    }

   /**
     * Manages query parameters for the current URL based on the current route and $_GET params.
     * It allows for adding, modifying, and removing parameters in a single call.
     *
     * @param array $addOrModifyParams Associative array of parameters to add or overwrite (e.g., ['key' => 'value']).
     * @param array $removeParams A simple array of parameter keys to remove from the URL (e.g., ['key1', 'key2']).
     * @return string The newly generated URL.
     */
    public static function editUrlQueryParams(array $addOrModifyParams = [], array $removeParams = []): string
    {
        foreach ($addOrModifyParams as $key => $value) {
            if (!is_string($key) || preg_match('/[^\w.-]/', $key)) {
                throw new \InvalidArgumentException('Invalid character in parameter key. Only word characters, dots, and hyphens are allowed.');
            }
            if (!is_scalar($value) && !is_null($value)) {
                throw new \InvalidArgumentException('Invalid value type. Only scalar values or null are allowed.');
            }
        }

        foreach ($removeParams as $param) {
            if (!is_string($param) || preg_match('/[^\w.-]/', $param)) {
                throw new \InvalidArgumentException('Invalid character in parameter to remove. Only word characters, dots, and hyphens are allowed.');
            }
        }

        $uri   = Yii::app()->request->getRequestUri();
        $parts = parse_url($uri);

        $queryParams = [];
        if (isset($parts['query']) && $parts['query'] !== '') {
            parse_str($parts['query'], $queryParams);
        }

        foreach ($removeParams as $param) {
            unset($queryParams[$param]);
        }

        foreach ($addOrModifyParams as $key => $value) {
            $queryParams[$key] = $value;
        }

        $newQuery = http_build_query($queryParams);

        $newUri = ($parts['path'] ?? '');
        if ($newQuery !== '') {
            $newUri .= '?' . $newQuery;
        }
        if (isset($parts['fragment'])) {
            $newUri .= '#' . $parts['fragment'];
        }

        return $newUri;
    }
}
