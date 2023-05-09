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
}
