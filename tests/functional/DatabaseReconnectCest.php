<?php 

class DatabaseReconnectCest
{
    public function _before(FunctionalTester $I)
    {
        $I->disconnectWebServerFromDatabase();
    }

    // tests
    public function tryReconnectToDBAfterSeveredConnection(FunctionalTester $I): void
    {
        $I->amOnPage('/');
        $I->cantSeeResponseCodeIs(500);
    }
}
