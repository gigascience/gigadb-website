<?php

use GigaDB\services\URLsService;

class UpdateFileSizeCest
{
    const testUrls = [
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142/Diagram-ALL-FIELDS-Check-annotation.jpg",
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142/readme.txt",
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142/SRAmetadb.zip",
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142",
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142/"
    ];

    public function _before(\FunctionalTester $I)
    {

    }


    public function tryFetchFileSizeFromFilesUrl(\FunctionalTester $I)
    {
        $expectedLengthList = [
            55547,
            2351,
            383892184,
            0,
            0,
        ];

        $u = new URLsService(self::testUrls);
        $I->assertTrue(is_a($u,"GigaDB\\services\\URLsService"));

        $contentLengthList = $u->fetchResponseHeader("Content-Length");

//        foreach ($expectedLengthList as $index => $expectedLength)
//        {
//            $I->assertEquals($expectedLength, array_values($contentLengthList)[$index]);
//        }

    }

    #[Codeception\Attribute\Skip]
    public function tryUpdateFileSizeWhenContentLengthInBytes(\FunctionalTester $I)
    {
    }


}
