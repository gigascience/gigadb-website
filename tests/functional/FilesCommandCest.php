<?php

/**
 * Functional tests for the file URLs checker command
 *
 */
class FilesCommandCest
{


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
            "location" => "https://mirror.in2p3.fr/pub/epel/8/Everything/x86_64/Packages/f",
            "extension" => "txt",
            "size" => "999",
            "format_id" => 1,
            "type_id" => 1,
        ]);

        $I->haveInDatabase("file", [
            "dataset_id" => 700,
            "name" => "another_stuff",
            "location" => "https://mirror.in2p3.fr/pub/epel/8/Everything/x86_64/Packages/f/",
            "extension" => "txt",
            "size" => "999",
            "format_id" => 1,
            "type_id" => 1,
        ]);



        $output = shell_exec("./protected/yiic files checkUrls --doi=300070");
        $I->assertContains("https://mirror.in2p3.fr/pub/epel/8/Everything/x86_64/Packages/f", $output);
        $I->assertContains("https://mirror.in2p3.fr/pub/epel/8/Everything/x86_64/Packages/f/", $output);
    }
}
