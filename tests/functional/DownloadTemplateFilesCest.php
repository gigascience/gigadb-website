<?php 

class DownloadTemplateFilesCest
{
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function tryToDownloadTemplateFile(FunctionalTester $I)
    {
        shell_exec('curl --url "http://gigadb.test/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx" -O');
        $I->assertFileExists("GigaDBUploadForm-forWebsite-v22Dec2021.xlsx");
    }

    public function tryToDownloadExampleFile1(FunctionalTester $I)
    {
        shell_exec('curl --url "http://gigadb.test/files/templates/GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx" -O');
        $I->assertFileExists("GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx");
    }

    public function tryToDownloadExampleFile2(FunctionalTester $I)
    {
        shell_exec('curl --url "http://gigadb.test/files/templates/GigaDBUpload-Example2-forWebsite-v22Dec2021.xlsx" -O');
        $I->assertFileExists("GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx");
    }
}
