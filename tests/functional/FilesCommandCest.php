<?php

/**
 * Functional tests for functions in FilesCommand
 *
 */
class FilesCommandCest
{
    public function _before(FunctionalTester $I)
    {
        // Remove md5 file attribute values for penguin dataset in database
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10669'));
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10670'));
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10671'));
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10672'));
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10673'));
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10674'));
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10675'));
    }

    /**
     * Check actionUpdateMD5FileAttribute($doi) function in FilesCommand
     */
    public function tryToUpdateMD5FileAttribute(FunctionalTester $I)
    {
        // Confirm there's no md5 file attribute values for penguin dataset
        $I->seeInDatabase('file_attributes', ['id' => '10669', 'value' => '']);
        $I->seeInDatabase('file_attributes', ['id' => '10670', 'value' => '']);
        $I->seeInDatabase('file_attributes', ['id' => '10671', 'value' => '']);
        $I->seeInDatabase('file_attributes', ['id' => '10672', 'value' => '']);
        $I->seeInDatabase('file_attributes', ['id' => '10673', 'value' => '']);
        $I->seeInDatabase('file_attributes', ['id' => '10674', 'value' => '']);
        $I->seeInDatabase('file_attributes', ['id' => '10675', 'value' => '']);

        // Execute FileCommand function to update md5 values for penguin dataset 100006
        $output = shell_exec("./protected/yiic files updateMD5FileAttribute --doi=100006");
        echo $output;

        // Assert expected md5 values in file attributes table
        $I->seeInDatabase('file_attributes', ['id' => '10669', 'value' => '826b699c854cc0f06e982d836410a81b']);
        $I->seeInDatabase('file_attributes', ['id' => '10670', 'value' => '47b8f47ca31cfd06d5ad62ceceb99860']);
        $I->seeInDatabase('file_attributes', ['id' => '10671', 'value' => '43b35c4e828bed20dbb071d2c5a40f17']);
        $I->seeInDatabase('file_attributes', ['id' => '10672', 'value' => '5afc9d8348bf4b52ee6e9c2bae9fd542']);
        $I->seeInDatabase('file_attributes', ['id' => '10673', 'value' => 'bd9bed43475eaa22b6ab62b9fb7a3909']);
        $I->seeInDatabase('file_attributes', ['id' => '10674', 'value' => '55c764721558086197bfbd663e1567a6']);
        $I->seeInDatabase('file_attributes', ['id' => '10675', 'value' => '826b699c854cc0f06e982d836410a81b']);
    }

    public function tryNotToOutputResolvableLinks(FunctionalTester $I)
    {

        $I->haveInDatabase("file", [
            "dataset_id" => 700,
            "name" => "GigaDBUploadForm-forWebsite-v22Dec2021.xlsx",
            "location" => "http://gigadb.test/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx",
            "extension" => "txt",
            "size" => "999",
            "format_id" => 1,
            "type_id" => 1,
        ]);


        $output = shell_exec("./protected/yiic files checkUrls --doi=300070");
        $I->assertNull($output);
    }

    public function tryToOutputNonResolvableHTTPLinks(FunctionalTester $I)
    {

        $I->haveInDatabase("file", [
            "dataset_id" => 700,
            "name" => "bogus.xlsx",
            "location" => "http://gigadb.test/files/templates/bogus.xlsx",
            "extension" => "txt",
            "size" => "999",
            "format_id" => 1,
            "type_id" => 1,
        ]);

        $output = shell_exec("./protected/yiic files checkUrls --doi=300070");
        $I->assertContains("http://gigadb.test/files/templates/bogus.xlsx", $output);
    }


    public function tryToOutputNonResolvableFTPLinks(FunctionalTester $I)
    {

        $I->haveInDatabase("file", [
            "dataset_id" => 700,
            "name" => "bogus.txt",
            "location" => "ftp://example.shiny/bogus.txt",
            "extension" => "txt",
            "size" => "999",
            "format_id" => 1,
            "type_id" => 1,
        ]);

        $output = shell_exec("./protected/yiic files checkUrls --doi=300070");
        $I->assertContains("ftp://example.shiny/bogus.txt", $output);
    }


    public function tryToOutputNonResolvableDirectoryLinks(FunctionalTester $I)
    {

        $I->haveInDatabase("file", [
            "dataset_id" => 700,
            "name" => "some_stuff",
            "location" => "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100020",
            "extension" => "txt",
            "size" => "999",
            "format_id" => 1,
            "type_id" => 1,
        ]);

        $I->haveInDatabase("file", [
            "dataset_id" => 700,
            "name" => "another_stuff",
            "location" => "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100020/",
            "extension" => "txt",
            "size" => "999",
            "format_id" => 1,
            "type_id" => 1,
        ]);



        $output = shell_exec("./protected/yiic files checkUrls --doi=300070");
        $I->assertContains("https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100020", $output);
        $I->assertContains("https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100020/", $output);
    }
}
