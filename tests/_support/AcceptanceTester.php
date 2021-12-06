<?php
/**
 * Code for implementing generic steps of feature files (non-generic should be in their support class)
 *
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
     * @Given I am on :page
     */
    public function iAmOn($page)
    {
        $this->amOnPage("$page");
    }

    /**
     * @When I follow :link
     */
    public function iFollow($link)
    {
        $this->click($link);
    }

    /**
     * @Then I should see :text
     */
    public function iShouldSee($text)
    {
        $this->see($text);
    }

    /**
     * @Then I should not see :text
     */
    public function iShouldNotSee($text)
    {
        $this->dontSee($text);
    }

    /**
     * @Then I should see a text field :id
     */
    public function iShouldSeeATextField($id)
    {
        $this->seeElement('input',['id' => $id, 'type' => "text"]);
    }

    /**
     * @Then I should see a password field :id
     */
    public function iShouldSeeAPasswordField($id)
    {
        $this->seeElement('input',['id' => $id, 'type' => "password"]);
    }


    /**
     * @Then I should see a drop-down field :id with values
     */
    public function iShouldSeeADropdownFieldWithValues($id, \Behat\Gherkin\Node\TableNode $preferredLinks)
    {
        $this->seeElement('select',['id' => $id]);

        foreach ($preferredLinks->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            $this->seeElement('option',[ 'value'=> $row[0] ]);
        }
    }

    /**
     * @Then I should see a check-box field :id
     */
    public function iShouldSeeACheckboxField($id)
    {
        $this->seeElement('input',['id' => $id, 'type' => "checkbox"]);
    }

    /**
     * @Then I should see a link :title to :url
     */
    public function iShouldSeeALinkTo($title, $url)
    {
        $this->seeLink($title,$url);
    }

    /**
     * @Then I should see an image located in :partialPath
     */
    public function iShouldSeeAnImageLocatedIn($partialPath)
    {
        $this->seeElement("//img[contains(@src, '$partialPath')]");
    }
    /**
     * @Then I should see a submit button :value
     */
    public function iShouldSeeASubmitButton($value)
    {
        $this->seeElement('input',['value' => $value, 'type' => "submit"]);
    }


}
