<?php

/**
 * featureFlagCest: test integration between configuration in main Yii app, PHP usage for feature flag and connection to Gitlab
 *
 * generated with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:cest functional featureFlagCest.php
 *
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run functional featureFlagCest
 *
 * NOTE: because caching is disabled, those tests will break if you don't have working connection to gitlab.com
 */
class featureFlagCest
{
    /**
     * @var array config holds the main Yii app configuration
     */
    private array $config;

    /**
     * @var CApplicationComponent holds an instance of the feature flag service under test
     */
    private CApplicationComponent $featureFlag;

    public function _before(FunctionalTester $I): void
    {
        $this->config = require(__DIR__."/../../protected/config/main.php");
        $this->featureFlag = new featureFlagService();
        $this->featureFlag->setCacheTtl(0); //otherwise functionality under test is not exercised after first test
    }

    // tests
    public function tryCheckIsEnabledForEnvEnabled(FunctionalTester $I): void
    {
        $this->featureFlag->setFflagUrl($this->config['components']['featureFlag']['fflagUrl']);
        $this->featureFlag->setFflagAppName($this->config['components']['featureFlag']['fflagAppName']);
        $this->featureFlag->setFflagInstanceId($this->config['components']['featureFlag']['fflagInstanceId']);
        $this->featureFlag->init();

        $I->assertTrue($this->featureFlag->isEnabled("fuw"));
    }

    // tests
    public function tryCheckIsNotEnabledForEnvNotEnabled(FunctionalTester $I): void
    {
        $this->featureFlag->setFflagUrl($this->config['components']['featureFlag']['fflagUrl']);
        $this->featureFlag->setFflagAppName("live");
        $this->featureFlag->setFflagInstanceId($this->config['components']['featureFlag']['fflagInstanceId']);
        $this->featureFlag->init();

        $I->assertFalse($this->featureFlag->isEnabled("fuw"));
    }

    // tests
    public function tryCheckIsNotEnabledForNonExistentFeature(FunctionalTester $I): void
    {
        $this->featureFlag->setFflagUrl($this->config['components']['featureFlag']['fflagUrl']);
        $this->featureFlag->setFflagAppName($this->config['components']['featureFlag']['fflagAppName']);
        $this->featureFlag->setFflagInstanceId($this->config['components']['featureFlag']['fflagInstanceId']);
        $this->featureFlag->init();

        $I->assertFalse($this->featureFlag->isEnabled("hollycow"));
    }
}
