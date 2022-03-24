<?php 

class MapBrowseCest
{
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function tryToSeeCoordinatesInPageSource(FunctionalTester $I)
    {
        $I->amOnPage("/site/mapbrowse");
        $I->seeResponseCodeIs(200);
        $html =$I->grabPageSource();
        $I->assertContains('"coordinates":[-45.295999 ,-60.345999]', $html, "Coordinate not found!");
        $I->assertContains('"coordinates":[114.4698 ,38.0360]', $html, "Coordinate not found!");

    }
}
