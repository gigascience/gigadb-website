<?php 

class MapBrowseCest
{
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function tryToSeeCoordinatesInPageSource(FunctionalTester $I)
    {
        // Get the coordinates in latitude and longitude format
        $testFixture = array_map('str_getcsv', file('/var/www/data/dev/sample_attribute.csv'));
        $expectPenguinCoordinate = $testFixture[5][3];

        // Change coordinates in XY format by swapping the position
        $expectPenguinCoordinateXY = implode(',', array_reverse(explode(',',$expectPenguinCoordinate)));

        $I->amOnPage("/site/mapbrowse");
        $I->canSeeInSource($expectPenguinCoordinateXY);
    }
}
