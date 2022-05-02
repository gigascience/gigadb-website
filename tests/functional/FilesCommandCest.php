<?php

/**
 * Functional tests for the file URLs checker command
 *
 */
class FilesCommandCest
{


    public function tryNotToOutputResolvableLinks(FunctionalTester $I)
    {
        $output = shell_exec("./protected/yiic files checkUrls --doi=100020");
        $I->assertNull($output);
    }

    public function tryToOutputNonResolvableLinks(FunctionalTester $I)
    {

        $I->haveInDatabase("file", [
            "dataset_id" => 22,
            "name" => "bogus.txt",
            "location" => "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100020/bogus.txt",
            "extension" => "txt",
            "size" => "999",
            "format_id" => 1,
            "type_id" => 1,
        ]);

        $output = shell_exec("./protected/yiic files checkUrls --doi=100020");
        $I->assertContains("https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100020/bogus.txt", $output);
    }
}
