<?php

use Facebook\WebDriver\WebDriverKeys;
use Codeception\Util\Locator;

/**
 * Code for implementing generic steps of feature files (non-generic should be in their support class)
 *
 * stubs copied from (after gherkin scenario steps are created):
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept g:snippets acceptance
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
        $this->seeElement('input', ['id' => $id, 'type' => "text"]);
    }

    /**
     * @Then I should see a password field :id
     */
    public function iShouldSeeAPasswordField($id)
    {
        $this->seeElement('input', ['id' => $id, 'type' => "password"]);
    }

    /**
     * Looks for a button with a name that is the content in between
     * it's button tags.
     *
     * @Then I should see a :button button
     */
    public function iShouldSeeAButton($button)
    {
        $this->seeElement(Locator::contains('button', $button));
    }

    /**
     * @Then I should see a drop-down field :id with values
     */
    public function iShouldSeeADropdownFieldWithValues($id, \Behat\Gherkin\Node\TableNode $preferredLinks)
    {
        $this->seeElement('select', ['id' => $id]);

        foreach ($preferredLinks->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            $this->seeElement('option', [ 'value' => $row[0] ]);
        }
    }

    /**
     * @Then I should see a check-box field :id
     */
    public function iShouldSeeACheckboxField($id)
    {
        $this->seeElement('input', ['id' => $id, 'type' => "checkbox"]);
    }

    /**
     * @Then I should see a link :title to :url
     */
    public function iShouldSeeALinkTo($title, $url)
    {
        $this->seeLink($title, $url);
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
        $this->seeElement('input', ['value' => $value, 'type' => "submit"]);
    }

    /**
     * @Then I should see a disabled submit button :value
     */
    public function iShouldSeeADisabledSubmitButton($value)
    {
        $this->seeElement('input', ['type' => 'submit', 'value' => $value, 'aria-disabled' => 'true']);
    }

    /**
     * @Then I should see a disabled file input for :file
     */
    public function iShouldSeeADisbledFileInputFor($file)
    {
        $this->seeElement('input', ['type' => 'file', 'name' => $file, 'aria-disabled' => 'true']);
    }

    /**
     * @Then I should see a button :button with curation log link
     */
    public function iShouldSeeAButtonWithLink($expectButton)
    {
        $actualButton = $this->grabTextFrom("//a[contains(@href, '/curationLog/create/id/')]");
        $this->assertEquals($actualButton, $expectButton);
    }

    /**
     * @When I fill in the field of :attribute :fieldName with :value
     */
    public function iFillInTheFieldOfWith($attribute, $fieldName, $value)
    {
        $this->fillField([$attribute => $fieldName], $value);
    }

    /**
     * @When I select :option from the field :fieldName
     */
    public function iSelectFromTheField($option, $fieldName)
    {
        $this->selectOption(["id" => $fieldName], $option);
    }

    /**
     * @When I check the field :fieldName
     */
    public function iCheckTheField($fieldName)
    {
        $this->checkOption(["id" => $fieldName]);
    }

    /**
     * @When I press the button :buttonName
     */
    public function iPressTheButton($buttonName)
    {
        $this->click($buttonName);
    }

    /**
     * @Then I should not see an affiliate login option for :provider
     */
    public function iShouldNotSeeLoginOption($provider)
    {
        $this->dontSeeElement("//a[contains(@href, '/opauth/$provider')]");
        $this->dontSeeElement("//img[contains(@src, '/images/new_interface_image/$provider.png')]");
    }
    /**
     * @Then I make a screenshot called :arg1
     */
    public function iMakeAScreenshot($arg1)
    {
        $this->makeScreenshot($arg1);
    }

    /**
     * @Then I go to a page tab :arg1
     */
    public function iGoToAPageTab($arg1)
    {
        $this->amOnPage($arg1);
        $this->reloadPage();
        $this->canSeeCurrentUrlEquals($arg1);
    }

    /**
     * @When I press return on the element :elementXPath
     */
    public function iPressReturnOnTheElement($elementXPath)
    {
        $this->pressKey($elementXPath, WebDriverKeys::RETURN_KEY);
    }

    /**
     * @When I wait :numberOf seconds
     */
    public function iWaitSeconds($numberOf)
    {
        $this->wait($numberOf);
    }

    /**
     * @Then I should see an element has id :id with class :class
     */
    public function iShouldSeeAnElementWith($id, $class)
    {
        $this->seeElement(['id' => $id], ['class' => $class]);
    }

    /**
     * Files need to be made available for tests in _data directory
     *
     * @When I attach the file :file to the file input element :file_input_element_name
     */
    public function iAttachTheFileToTheFileInputElement($file, $file_input_element_name)
    {
        $this->attachFile("input[name=$file_input_element_name]", $file);
    }

    /**
     * @Then I should see an image field :field with text :value
     */
    public function iShuldSeeAnImageFieldWithText($field, $value)
    {
        $this->seeElement('input', ['name' => "Image[$field]", 'type' => "text", 'value' => "$value"]);
    }

    /**
     * @Then I should see an image :image is linked to :expectedUrl
     */
    public function iShouldSeeAnImageIsLinkedTo($image, $expectedUrl)
    {
        $this->seeElement("//img[@src='$image']");
        $actualUrl = $this->grabAttributeFrom("//img[@src='$image']/parent::*", "href");
        $this->assertEquals($expectedUrl, $actualUrl);
    }

    /**
     * @Then I should see a curation log action :action is linked to :expectedUrl
     */
    public function iShouldSeeACurationLogActionIsLinkedTo($action, $expectedUrl)
    {
        $this->seeElement("//a[@title='$action']");
        $actualUrl = $this->grabAttributeFrom("//a[@title='$action']", "href");
        $this->assertEquals($expectedUrl, $actualUrl);
    }

    /**
     * @Then I click on curation log action :action
     */
    public function iClickOnCurationLogAction($action)
    {
        $this->seeElement("//a[@title='$action']");
        $this->click($action);
    }

    /**
     * @When I click the table settings for :table
     */
    public function iClickTheTableSettings($table)
    {
        $this->click("#" . $table);
        $this->wait(1);
    }

    /**
     * @Then I should be on :path
     */
    public function iShouldBeOn($path)
    {
        $this->seeCurrentUrlEquals($path);
    }

    /**
     * @Then I should see :checkbox checkbox is not checked
     */
    public function iShouldSeeCheckboxIsNotChecked($checkbox)
    {
        $this->dontSeeCheckboxIsChecked("//input[@id='$checkbox']");
    }

    /**
     * @Then I should see :checkbox checkbox is checked
     */
    public function iShouldSeeCheckboxIsChecked($checkbox)
    {
        $this->seeCheckboxIsChecked("//input[@id='$checkbox']");
    }

    /**
     * @Then I check :checkbox checkbox
     */
    public function iCheckCheckbox($checkbox)
    {
        $this->checkOption("//input[@id='$checkbox']");
    }

    /**
     * @Then I uncheck :checkbox checkbox
     */
    public function iUncheckCheckbox($checkbox)
    {
        $this->uncheckOption("//input[@id='$checkbox']");
    }

    /**
     * @Then I should see current url contains :value
     */
    public function iShouldSeeCurrentUrlContains($value)
    {
        $this->seeInCurrentUrl($value);
    }

    /**
     * @When I confirm to :message
     */
    public function iConfirmTo($message)
    {
        $this->seeInPopup($message);
        $this->acceptPopup();
    }

    /**
     * @When I go to the new tab
     */
    public function iGoToTheNewTab()
    {
        $this->switchToNextTab();
    }

    /**
     * @Then I should see an input button :button
     */
    public function iShouldSeeAnInputButton($button)
    {
        $this->seeElement('input', ['type' => "button", 'value' => $button]);
    }

    /**
     * @Then I should not see an input button :button
     */
    public function iShouldNotSeeAnInputButton($button)
    {
        $this->dontSeeElement('input', ['type' => "button", 'value' => $button]);
    }
}
