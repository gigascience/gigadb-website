<?php

declare(strict_types=1);

namespace app\components;

use GigaDB\models\Dataset;
use GigaDB\models\File;
use GigaDB\services\URLsService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Exception\ConnectException;
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

    /**
     * Method to check the validity for the all the files of the dataset identified with $doi
     *
     * @return array returns the list of urls with problems
     * @throws Exception|GuzzleException
     */
    public function checkURLs(array $urls): array
    {
        $detectsRedirectsAndDirectories = function ($response, $url) {
            if (301 === $response->getStatusCode() || str_ends_with($url, "/")) {
                return -1;
            }

            return null;
        };


        $badUrls = [];
        foreach ($urls as $url) {
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
                $response = $this->webClient->head($url, ['timeout' => self::TIMEOUT]);
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
            } catch (TransferException $e) {
                $badUrls[$url] = "Request time out, because resource could not be downloaded under " . self::TIMEOUT . " seconds";
                continue;
            } catch (ConnectException $e) {
                $badUrls[$url] = "Network error";
                continue;
            }
        }

        return $badUrls;
    }
}
