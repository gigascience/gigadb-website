<?php

declare(strict_types=1);

namespace functional;

use FunctionalTester;
use SimpleXMLElement;
use Yii;

class SiteCest
{
    public function tryToShowRssFeedAsXml(FunctionalTester $I) {
        $I->amOnPage('/site/feed');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeHttpHeader('Content-Type', 'text/xml;charset=UTF-8');
        $I->seeResponseIsXml();

        $response = $I->grabResponse();
        $feed = new SimpleXMLElement($response);
        $I->assertEquals('10.80027/102484', $feed->channel->item[0]->guid);
        $I->assertEquals('10.80027/100142', $feed->channel->item[3]->guid);
    }
}
