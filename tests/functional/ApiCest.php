<?php

/**
 * Class ApiCest
 *
 * generated with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:cest functional CaptchaCest.php
 *
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run functional CaptchaCest
 */
class ApiCest
{
    public function _before(ApiTester $I)
    {
    }

    /**
     * @param ApiTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function tryOutputDatasetOnly(ApiTester $I) {
        $url = "/api/dataset/doi/100006?result=dataset" ;
        # load target url
        $I->sendGet($url);

//        // Go to a page and getting xml content
//        $feed = $this->getXMLWithSessionAndUrl($url);
//        print_r($feed);
//
//        // Validate text presence on a page.
//        $this->assertEquals("Genomic data from Adelie penguin (Pygoscelis adeliae). ", $feed->dataset->title);
//        $this->assertNull($feed->samples->sample);
//        $this->assertNull($feed->files->file);
//
//        $targetUrl = "/user/create";
//
//        # load target url
//        $I->amOnPage($targetUrl);
//        # find captcha image
//        $I->seeElement("//div/img[@style='width:200px;']");
//        # Get the source of the image
//        $imgSrc1 = $I->grabAttributeFrom("//div/img[@style='width:200px;']",'src');
//        # ensure it's not null and it's an image
//        $I->assertContains("image/jpeg;base64",$imgSrc1);
//        # load the target url again
//        $I->amOnPage($targetUrl);
//        # Get the source of the image
//        $imgSrc2 = $I->grabAttributeFrom("//div/img[@style='width:200px;']",'src');
//        # ensure it's not null and it's an image
//        $I->assertContains("image/jpeg;base64",$imgSrc2);
//        # make sure both content are different
//        $I->assertNotEquals($imgSrc1, $imgSrc2);
    }
}
