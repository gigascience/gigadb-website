<?php

use Unleash\Client\UnleashBuilder;
use Unleash\Client\Unleash;
use Buzz\Browser;
use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * FeatureFlagService
 *
 * A service to facilitate the use of Gitlab feature flags across the codebase without messing with
 * configuration and connection details each time
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FeatureFlagService extends CApplicationComponent
{
    // configuration parameters
    /**
     * @var string|null $fflagUrl Holds the URL value.
     */
    private ?string $fflagUrl;

    /**
     * @var string|null $fflagAppName Holds the application name value.
     */
    private ?string $fflagAppName;

    /**
     * @var int|null $fflagInstanceId Holds the instance ID value.
     */
    private ?string $fflagInstanceId;


    // configured Unleashed instance
    /**
     * @var Unleash $unleashed Holds the configured Unleashed instance
     */
    private \Unleash\Client\Unleash $unleashed;

    // Cache control
    /**
     * @var int $cacheTTL time to live in cache for the flag value, default to 60 s
     */
    private int $cacheTTL = 60;

    /**
     * init()
     * Initialise the Unleashed instance using application configuration
     *
     * Note: no PSR-12 signature because it overrides third party parent's method which also has no PSR-12 signature
     *
     * @return void
     */
    public function init() {
        parent::init();

        $client = new FileGetContents(new Psr17Factory());
        $factory = new Psr17Factory();

        $this->unleashed = UnleashBuilder::create()
            ->withAppName($this->fflagAppName)
            ->withAppUrl($this->fflagUrl)
            ->withInstanceId($this->fflagInstanceId)
            ->withHttpClient($client)
            ->withRequestFactory($factory)
            ->withCacheTimeToLive($this->cacheTTL)
            ->build();
    }

    /**
     * Return whether the given feature is enabled or not
     *
     * @param string $feature
     * @return bool
     */
    public function isEnabled(string $feature): bool
    {
        return $this->unleashed->isEnabled($feature);
    }

    /**
     * Getter for $fflagUrl
     *
     * @return string|null The URL value.
     */
    public function getFflagUrl(): ?string
    {
        return $this->fflagUrl;
    }

    /**
     * Setter for $fflagUrl
     *
     * @param string $fflagUrl The URL value.
     * @return void
     */
    public function setFflagUrl(string $fflagUrl): void
    {
        $this->fflagUrl = $fflagUrl;
    }

    /**
     * Getter for $fflagAppName
     *
     * @return string|null The application name value.
     */
    public function getFflagAppName(): ?string
    {
        return $this->fflagAppName;
    }

    /**
     * Setter for $fflagAppName
     *
     * @param string $fflagAppName The application name value.
     * @return void
     */
    public function setFflagAppName(string $fflagAppName): void
    {
        $this->fflagAppName = $fflagAppName;
    }

    /**
     * Getter for $fflagInstanceId
     *
     * @return string|null The instance ID value.
     */
    public function getFflagInstanceId(): ?string
    {
        return $this->fflagInstanceId;
    }

    /**
     * Setter for $fflagInstanceId
     *
     * @param string $fflagInstanceId The instance ID value.
     * @return void
     */
    public function setFflagInstanceId(string $fflagInstanceId): void
    {
        $this->fflagInstanceId = $fflagInstanceId;
    }

    /**
     * Getter for $cacheTTL
     *
     * @return int|null The Cache TTL value.
     */
    public function getCacheTtl(): ?int
    {
        return $this->cacheTTL;
    }

    /**
     * Setter for $cacheTTL
     *
     * @param int $cacheTTL The instance ID value.
     * @return void
     */
    public function setCacheTtl(string $cacheTTL): void
    {
        $this->cacheTTL = $cacheTTL;
    }


}