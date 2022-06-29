<?php

/**
 * Functional tests for functions in FilesCommand
 *
 */
class FilesCommandCest
{
    public function tryToUpdateMD5FileAttribute(FunctionalTester $I)
    {
        // Update file attributes for dataset 100006 with dummy md5 checksums
        $I->updateInDatabase('users', array('isAdmin' => true), array('email' => 'miles@davis.com'));
        // Execute FileCommand function to update md5 values for dataset 100006
        $output = shell_exec("./protected/yiic files updateMD5FileAttribute --doi=100006");
        echo $output;
        // Assert expected md5 values in file attributes table
        $I->seeInDatabase('users', ['name' => 'Davert', 'email' => 'davert@mail.com']);
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
