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
    public function tryUpdateSampleWithOneNonExistAttribute(FunctionalTester $I)
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

    public function tryUpdateSampleWithTwoNonExistAttribute(FunctionalTester $I)
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

    public function tryCreateSampleWithEmptySTaxonId(FunctionalTester $I)
    {
        $I->amOnPage("/adminSample/create");
        $I->canSee("Create Sample");
        $I->fillField("Sample[species_id]", "");
        $I->click("Create");
        $I->canSee("Please fix the following input errors:");
        $I->canSee("Taxon ID is empty!");
    }
    public function tryCreateSampleWithNonNumericTaxonId(FunctionalTester $I)
    {
        $I->amOnPage("/adminSample/create");
        $I->canSee("Create Sample");
        $I->fillField("Sample[species_id]", "Human");
        $I->click("Create");
        $I->canSee("Please fix the following input errors:");
        $I->canSee("Taxon ID Human is not numeric!");
    }

    public function tryCreateSampleWithNonExistTaxonId(FunctionalTester $I)
    {
        $I->amOnPage("/adminSample/create");
        $I->canSee("Create Sample");
        $I->fillField("Sample[species_id]", "789123");
        $I->click("Create");
        $I->canSee("Please fix the following input errors:");
        $I->canSee("Taxon ID 789123 is not found!");
        $I->dontSeeInDatabase("species", [
            "tax_id" => "789123"
        ]);
    }
    public function tryCreateSampleWithOneNonExistAttribute(FunctionalTester $I)
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

    public function tryCreateSampleWithTwoNonExistAttribute(FunctionalTester $I)
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

    public function tryCreateSampleWithExistAttribute(FunctionalTester $I)
    {
        $I->amOnPage("/adminSample/create");
        $I->canSee("Create Sample");
        $I->fillField("Sample[species_id]", "87676:Eucalyptus pauciflora");
        $I->fillField("Sample[attributesList]", "sex=\"male\"");
        $I->click("Create");
        $I->canSeeInDatabase("sample", [
            "id" => "433",
            "species_id" => "100",
            "name" => "SAMPLE:SRS188811"
        ]);
        $I->canSeeInDatabase("sample_attribute", [
            "sample_id" => "433",
            "attribute_id" => "200",
            "value" => "male"
        ]);
    }
}
