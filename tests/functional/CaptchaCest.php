<?php

/**
 * Class CaptchaCest
 *
 * generated with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:cest functional CaptchaCest.php
 *
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run functional CaptchaCest
 */
class CaptchaCest
{
    public function _before(FunctionalTester $I)
    {
    }

    /**
     * @param FunctionalTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function tryLoadDistinctCaptchaOnUserForm(FunctionalTester $I)
    {
        $targetUrl = "/user/create";

        # load target url
        $I->amOnPage($targetUrl);
        # find captcha image
        $I->seeElement("//div/img[@style='width:200px;']");
        # Get the source url of the image
        $imgSrc1 = $I->grabAttributeFrom("//div/img[@style='width:200px;']",'src');
        # download content of captcha url
        $I->amOnPage($imgSrc1);
        $img1 = $I->checksumOfResponse();
        # load the target url again
        $I->amOnPage($targetUrl);
        # download content of captcha url
        $imgSrc2 = $I->grabAttributeFrom("//div/img[@style='width:200px;']",'src');
        $I->amOnPage($imgSrc2);
        $img2 = $I->checksumOfResponse();
        # make sure both content are different
        $I->assertNotEquals($img1, $img2);
    }

    /**
     * @param FunctionalTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function tryLoadDistinctCaptchaOnContactForm(FunctionalTester $I)
    {
        $targetUrl = "/site/contact";

        # load target url
        $I->amOnPage($targetUrl);
        # find captcha image
        $I->seeElement("//div/img[@style='width:200px;']");
        # Get the source of the image
        $imgSrc1 = $I->grabAttributeFrom("//div/img[@style='width:200px;']",'src');
        # load the target url again
        $I->amOnPage($targetUrl);
        # Get the source of the image
        $imgSrc2 = $I->grabAttributeFrom("//div/img[@style='width:200px;']",'src');
        # make sure both content are different
        $I->assertNotEquals($imgSrc1, $imgSrc2);
    }
}
