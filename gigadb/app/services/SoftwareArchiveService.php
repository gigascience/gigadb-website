<?php

declare(strict_types=1);

namespace GigaDB\services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;
use yii\base\Component;

final class SoftwareArchiveService extends Component
{
    private function extractSwhid(string $input): ?string
    {
        if (preg_match('#(?:/)?swh:1:(?<type>dir|rev|snp|rel|cnt):(?<hash>[a-f0-9]{40})#', $input, $matches)) {
            return "swh:1:{$matches['type']}:{$matches['hash']}";
        }

        return null;
    }

    private function resolveSwhid(Client $client, string $swhid): ?array
    {
        try {
            $url = 'resolve/' . urlencode($swhid);
            $response = $client->get($url);

            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            return null;
        }
    }

    public function getOriginUrl(string $input): ?string
    {
        //is already an origin url
        if (preg_match('#/origin/(https?://.+)$#', $input, $matches)) {
            return null;
        }

        $input = html_entity_decode($input);
        //return query param
        if (preg_match('/[?&;]origin_url=(https?:\/\/[^&]+)/', $input, $matches) || preg_match('/[?&;]origin=(https?:\/\/[^&]+)/', $input, $matches)) {
            return $matches[1];
        }

        $client = new Client([
            'base_uri' => 'https://archive.softwareheritage.org/api/1/',
            'headers'  => [
                'User-Agent' => 'SWH-Origin-Finder/1.0'
            ]
        ]);

        $url = $this->extractSwhid($input);

        if (!$url) {
            return null;
        }

        $resolved = $this->resolveSwhid($client, $url);

        if (!$resolved) {
            return null;
        }

        if ($origin = $resolved['metadata']['origin']) {
            return $origin;
        }

        $type = $resolved['object_type'] ?? null;
        $hash = $resolved['object_id'] ?? null;

        switch ($type) {
            case 'revision':
                $url = 'revision/' . $hash;
                $res = $client->get($url);

                $data = json_decode($res->getBody()->getContents(), true);
                return $data['metadata']['origin'] ?? null;

            default:
                return null;
        }
    }
}
