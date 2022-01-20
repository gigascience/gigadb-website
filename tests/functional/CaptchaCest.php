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
        # Get the source of the image
        $imgSrc1 = $I->grabAttributeFrom("//div/img[@style='width:200px;']",'src');
        # ensure it's not null and it's an image
        $I->assertContains("image/jpeg;base64",$imgSrc1);
        # load the target url again
        $I->amOnPage($targetUrl);
        # Get the source of the image
        $imgSrc2 = $I->grabAttributeFrom("//div/img[@style='width:200px;']",'src');
        # ensure it's not null and it's an image
        $I->assertContains("image/jpeg;base64",$imgSrc2);
        # make sure both content are different
        $I->assertNotEquals($imgSrc1, $imgSrc2);
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
        # ensure it's not null and it's an image
        $I->assertContains("image/jpeg;base64",$imgSrc1);
        # load the target url again
        $I->amOnPage($targetUrl);
        # Get the source of the image
        $imgSrc2 = $I->grabAttributeFrom("//div/img[@style='width:200px;']",'src');
        # ensure it's not null and it's an image
        $I->assertContains("image/jpeg;base64",$imgSrc2);
        # make sure both content are different
        $I->assertNotEquals($imgSrc1, $imgSrc2);
    }
}
