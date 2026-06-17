<?php

declare(strict_types=1);

require_once __DIR__ . '/LoadingFixtureTrait.php';

use Codeception\Test\Unit;

class AutoCompleteServiceTest extends Unit
{
    use LoadingFixtureTrait;

    public function _before()
    {
        $db = $this->getModule('Db')->_getDbh();
        $db->exec('TRUNCATE TABLE species CASCADE');

        $this->loadFixture('species', \Species::class);
    }

    /**
    * test that it autoComplete can find terms from partial input
    *
    * @dataProvider speciesTermsToComplete
    **/
    public function testItShouldreturnSpeciesForTerm($term, $expected_result)
    {
        $autoComplete = new AutoCompleteService();

        $terms = $autoComplete->findSpeciesLike($term);
        $this->assertEquals($expected_result, $terms);
    }


    /**
     * set term and expection for all possible scenarios
     * of autoComplete for species
     *
     * @return array[], array[string, array]
     */
    public function speciesTermsToComplete()
    {
        return [
            "By common name" => ["guin", ["9238:Adelie penguin,Pygoscelis adeliae"]],
            "By scientific name" => ["pygo", ["9238:Adelie penguin,Pygoscelis adeliae", "9239:Pygoscelis Quattro"]],
            "is numeric" => [4555, ["4555:Foxtail millet,Setaria italica"]],
            "no match" => ["clown", []]
        ];
    }
}
