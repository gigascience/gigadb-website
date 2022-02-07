<?php

/**
 * Class DownloadTemplateFilesCest
 *
 * generated with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:cest functional DownloadTemplateFiles
 *
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run functional DownloadTemplateFiles
 */
class DownloadTemplateFilesCest
{
    public function _before(FunctionalTester $I)
    {
    }

    /**
     * @param FunctionalTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function tryDownloadTemplateFileAfterLogin (FunctionalTester $I)
    {
        $I->signInAsAUser();
        $I->amOnPage("/datasetSubmission/upload");
        $I->see("Download Template File");
        $I->sendGET("/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx");
        $I->canSeeResponseCodeIs(200);
    }

    /**
     * @param FunctionalTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function tryDownloadExampleFile1AfterLogin (FunctionalTester $I)
    {
        $I->signInAsAUser();
        $I->amOnPage("/datasetSubmission/upload");
        $I->see("Download Example File 1");
        $I->sendGET("/files/templates/GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx");
        $I->canSeeResponseCodeIs(200);
    }

    /**
     * @param FunctionalTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function tryDownloadExampleFile2AfterLogin (FunctionalTester $I)
    {
        $I->signInAsAUser();
        $I->amOnPage("/datasetSubmission/upload");
        $I->see("Download Example File 2");
        $I->sendGET("/files/templates/GigaDBUpload-Example2-forWebsite-v22Dec2021.xlsx");
        $I->canSeeResponseCodeIs(200);
    }

    /**
     * @param FunctionalTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function tryDownloadTemplateFileFromHelpPage (FunctionalTester $I)
    {
        $I->amOnPage("/site/help#guidelines");
        $I->see("Excel template file");
        $I->sendGET("/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx");
        $I->canSeeResponseCodeIs(200);
    }
}
