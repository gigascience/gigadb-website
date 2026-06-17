<?php

declare(strict_types=1);

namespace functional;

use FunctionalTester;
use NewsletterService;
use Yii;

class NewsletterCest
{
    public function subscribeNewUser(FunctionalTester $I)
    {
        $I->assertNotEmpty(getenv('MAILCHIMP_API_KEY'));
        $I->assertNotEmpty(getenv('MAILCHIMP_LIST_ID'));
        $I->assertNotEmpty(getenv('MAILCHIMP_TEST_EMAIL'));
        $today     = date('ymd');
        $fork      = getenv('FORK');
        $pid       = getmypid();
        $baseEmail = getenv('MAILCHIMP_TEST_EMAIL');
        $email     = "{$fork}{$today}{$pid}_{$baseEmail}";

        $service = new NewsletterService(getenv('MAILCHIMP_API_KEY'), getenv('MAILCHIMP_LIST_ID'));
        $result  = $service->addToMailing($email);
        $I->assertTrue($result, "$email should be added to Mailchimp");
    }

    public function unsubscribeUser(FunctionalTester $I) {
        $fork = getenv('FORK');
        $today = date('ymd');
        $pid = getmypid();
        $baseEmail = getenv('MAILCHIMP_TEST_EMAIL');
        $email = sprintf('%s%s%s_%s', $fork, $today, $pid, $baseEmail);

        $service = new NewsletterService(getenv('MAILCHIMP_API_KEY'), getenv('MAILCHIMP_LIST_ID'));
        $I->assertTrue(
            $service->removeFromMailing($email),
            "$email should be removed from Mailchimp"
        );
    }

    public function applicationComponentIsConfigured(FunctionalTester $I)
    {
        $appResult = Yii::app()->newsletter->getMailingListInfo();
        $I->assertTrue(
            (bool) $appResult,
            'Le composant application newsletter doit renvoyer les informations de liste'
        );
    }
}
