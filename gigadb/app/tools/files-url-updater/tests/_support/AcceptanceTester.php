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
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
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
     * @Given the tool is configured
     */
    public function theToolIsConfigured()
    {
        //TODO check config are valid
        true;
    }

    /**
     * @When I run the command :command with options :options
     */
    public function iRunTheCommandWithOptions($command, $options)
    {
        $this->runShellCommand("$command $options");

    }

    /**
     * @Then I should see :output
     */
    public function iShouldSee($output)
    {
        $this->seeInShellOutput($output);
    }


}
