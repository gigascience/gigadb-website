<?php 

class DownloadTemplateFilesCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
        if (file_exists("GigaDBUploadForm-forWebsite-v22Dec2021.xlsx"))
            unlink("GigaDBUploadForm-forWebsite-v22Dec2021.xlsx");

        if (file_exists("GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx"))
            unlink("GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx");

        if (file_exists("GigaDBUpload-Example2-forWebsite-v22Dec2021.xlsx"))
            unlink("GigaDBUpload-Example2-forWebsite-v22Dec2021.xlsx");
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
        $I->assertFileExists("GigaDBUpload-Example2-forWebsite-v22Dec2021.xlsx");
    }
}
