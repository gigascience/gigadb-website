<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

   /**
    * Define custom actions here
    */

    /**
     * @Given I am on :arg1
     */
    public function iAmOn($arg1)
    {
        $this->amOnPage("$arg1");
    }

    /**
     * @When I follow :arg1
     */
    public function iFollow($arg1)
    {
        $this->click($arg1);
    }

    /**
     * @Then I should see :arg1
     */
    public function iShouldSee($arg1)
    {
        $this->see($arg1);
    }

    /**
     * @Then I should not see :arg1
     */
    public function iShouldNotSee($arg1)
    {
        $this->dontSee($arg1);
    }

    /**
     * @Then I should see a text field :arg1
     */
    public function iShouldSeeATextField($arg1)
    {
        $this->seeElement('input',['id' => $arg1, 'type' => "text"]);
    }

    /**
     * @Then I should see a password field :arg1
     */
    public function iShouldSeeAPasswordField($arg1)
    {
        $this->seeElement('input',['id' => $arg1, 'type' => "password"]);
    }


    /**
     * @Then I should see a drop-down field :arg1 with values
     */
    public function iShouldSeeADropdownFieldWithValues($arg1, \Behat\Gherkin\Node\TableNode $preferredLinks)
    {
        $this->seeElement('select',['id' => $arg1]);

        foreach ($preferredLinks->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            $this->seeElement('option',[ 'value'=> $row[0] ]);
        }
    }

    /**
     * @Then I should see a check-box field :arg1
     */
    public function iShouldSeeACheckboxField($arg1)
    {
        $this->seeElement('input',['id' => $arg1, 'type' => "checkbox"]);
    }

    /**
     * @Then I should see a link :arg1 to :arg2
     */
    public function iShouldSeeALinkTo($arg1, $arg2)
    {
        $this->seeLink($arg1,$arg2);
    }

    /**
     * @Then I should see a submit button :arg1
     */
    public function iShouldSeeASubmitButton($arg1)
    {
        $this->seeElement('input',['value' => $arg1, 'type' => "submit"]);
    }


}
