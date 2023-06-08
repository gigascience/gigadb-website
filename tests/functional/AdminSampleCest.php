<?php

/**
 *
 * generated with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:cest functional AdminSampleCest
 *
 *run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run functional AdminSampleCest
 */
class AdminSampleCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amOnPage("/site/login");
        $I->submitForm('form.form-horizontal', [
            'LoginForm[username]' => 'admin@gigadb.org',
            'LoginForm[password]' => 'gigadb']);
        $I->canSee("Admin");
    }

    // tests
    public function tryUpdateAttributeListWithOneNonExistAttribute(FunctionalTester $I)
    {
        $I->amOnPage("/adminSample/update/id/432");
        $I->canSee("Update Sample 432");
        $I->fillField("Sample[attributesList]", "lat_lon=\"38.0,114.4\",animal=\"tiger\"");
        $I->click("Save");
        $I->canSee("Please fix the following input errors:");
        $I->canSee("Attribute name for the input animal=tiger is not valid - please select a valid attribute name!");
        $I->click("Save");
        $I->canSee("lat_lon=\"38.0,114.4\"");
        $I->canSeeInDatabase("sample_attribute", [
            "sample_id" => "432",
            "attribute_id" => "269",
            "value" => "38.0,114.4"
        ]);
        $I->dontSeeInDatabase("sample_attribute", [
            "sample_id" => "432",
            "value" => "tiger"
        ]);
    }

    public function tryUpdateAttributeListWithTwoNonExistAttribute(FunctionalTester $I)
    {
        $I->amOnPage("/adminSample/update/id/432");
        $I->canSee("Update Sample 432");
        $I->fillField("Sample[attributesList]", "lat_lon=\"38.0,114.4\",animal=\"tiger\",plant=\"rose\"");
        $I->click("Save");
        $I->canSee("Please fix the following input errors:");
        $I->canSee("Attribute name for the input animal=tiger is not valid - please select a valid attribute name!");
        $I->canSee("Attribute name for the input plant=rose is not valid - please select a valid attribute name!");
        $I->click("Save");
        $I->canSee("lat_lon=\"38.0,114.4\"");
        $I->canSeeInDatabase("sample_attribute", [
            "sample_id" => "432",
            "attribute_id" => "269",
            "value" => "38.0,114.4"
        ]);
        $I->dontSeeInDatabase("sample_attribute", [
            "sample_id" => "432",
            "value" => "tiger"
        ]);
        $I->dontSeeInDatabase("sample_attribute", [
            "sample_id" => "432",
            "value" => "rose"
        ]);
    }

    public function tryCreateAttributeListWithEmptySpeciesName(FunctionalTester $I)
    {
        $I->amOnPage("/adminSample/create");
        $I->canSee("Create Sample");
        $I->fillField("Sample[species_id]", "");
        $I->click("Create");
        $I->canSee("Please fix the following input errors:");
        $I->canSee("Species name is empty!");
    }
    public function tryCreateAttributeListWithNonExistSpeciesName(FunctionalTester $I)
    {
        $I->amOnPage("/adminSample/create");
        $I->canSee("Create Sample");
        $I->fillField("Sample[species_id]", "Human");
        $I->click("Create");
        $I->canSee("Please fix the following input errors:");
        $I->canSee("Species name Human is not found!");
    }

    public function tryCreateAttributeListWithNonExistSpeciesId(FunctionalTester $I)
    {
        $I->amOnPage("/adminSample/create");
        $I->canSee("Create Sample");
        $I->fillField("Sample[species_id]", "789123");
        $I->click("Create");
        $I->canSee("Please fix the following input errors:");
        $I->canSee("Species id 789123 is not found!");
        $I->dontSeeInDatabase("sample", [
            "species_id" => "789123"
        ]);
    }
    public function tryCreateAttributeListWithOneNonExistAttribute(FunctionalTester $I)
    {
        $I->amOnPage("/adminSample/create");
        $I->canSee("Create Sample");
        $I->fillField("Sample[species_id]", "87676:Eucalyptus pauciflora");
        $I->fillField("Sample[attributesList]", "animal=\"tiger\"");
        $I->click("Create");
        $I->canSee("Please fix the following input errors:");
        $I->canSee("Attribute name for the input animal=tiger is not valid - please select a valid attribute name!");
        $I->dontSeeInDatabase("sample_attribute", [
            "sample_id" => "432",
            "value" => "tiger"
        ]);
    }

    public function tryCreateAttributeListWithTwoNonExistAttribute(FunctionalTester $I)
    {
        $I->amOnPage("/adminSample/create");
        $I->canSee("Create Sample");
        $I->fillField("Sample[species_id]", "87676:Eucalyptus pauciflora");
        $I->fillField("Sample[attributesList]", "animal=\"tiger\",plant=\"rose\"");
        $I->click("Create");
        $I->canSee("Please fix the following input errors:");
        $I->canSee("Attribute name for the input animal=tiger is not valid - please select a valid attribute name!");
        $I->canSee("Attribute name for the input plant=rose is not valid - please select a valid attribute name!");
        $I->dontSeeInDatabase("sample_attribute", [
            "sample_id" => "432",
            "value" => "tiger"
        ]);
        $I->dontSeeInDatabase("sample_attribute", [
            "sample_id" => "432",
            "value" => "rose"
        ]);
    }
}
