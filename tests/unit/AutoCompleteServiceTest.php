<?php

class AutoCompleteServiceTest extends CDbTestCase
{
    protected $fixtures = array(
        'species' => 'Species',
    );

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
     * @return string, array[string, array]
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
