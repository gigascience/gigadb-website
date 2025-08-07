<?php

declare(strict_types=1);

namespace functional;

class SearchCest
{
    public function tryNewWithoutKeyword(\FunctionalTester $I)
    {
        $I->amOnPage('/search/new');
        $I->seeResponseCodeIs(200);
        $I->seeInCurrentUrl('/');
        $I->see('Keyword can not be blank', '.alert-danger');
    }

    public function tryNewGetWithKeyword(\FunctionalTester  $I)
    {
        $I->amOnPage('search/new?keyword=millet');
        $I->seeResponseCodeIs(200);
        $I->see('Showing 1 - 1 of 1 datasets', '#result-search-count');
    }


    public function tryNewGetWithKeywordNotFound(\FunctionalTester  $I)
    {
        $I->amOnPage('search/new?keyword=test1');
        $I->seeResponseCodeIs(200);
        $I->cantsee('Showing 1 - 1 of 1 datasets', '#result-search-count');
    }

    public function tryNewPostAjax(\FunctionalTester $I) {
        $I->amOnPage('/');
        $csrf = $I->grabAttributeFrom('meta[name="csrf-token"]', 'content');
        $I->haveHttpHeader('X-Requested-With', 'XMLHttpRequest');
        $I->sendPOST('search/new?keyword=test', ['page' => 1, 'YII_CSRF_TOKEN' => $csrf]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['success' => true]);

        $json = $I->grabResponse();
        $data = json_decode($json, true);
        $I->assertArrayHasKey('success', $data);
        $I->assertTrue($data['success']);
        $I->assertArrayHasKey('filter', $data);
        $I->assertArrayHasKey('result', $data);
        $I->assertArrayHasKey('range', $data);
    }
}
