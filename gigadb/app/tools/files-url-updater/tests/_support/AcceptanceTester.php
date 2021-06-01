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
//        throw new \PHPUnit\Framework\IncompleteTestError("Step `the tool is configured` is not defined");
        true;
    }

    /**
     * @When I run the command :arg1 with options :arg2
     */
    public function iRunTheCommandWithOptions($arg1, $arg2)
    {
//        throw new \PHPUnit\Framework\IncompleteTestError("Step `I run the command :arg1 with options :arg2` is not defined");
        true;
    }

    /**
     * @Then I should see :arg1
     */
    public function iShouldSee($arg1)
    {
//        throw new \PHPUnit\Framework\IncompleteTestError("Step `I should see :arg1` is not defined");
        true;
    }


}
