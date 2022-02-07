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
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

   /**
    * Define custom actions here
    */
   public function signInAsAUser()
   {
       $this->amOnPage("/site/login");
       $this->fillField(["id" => "LoginForm_username"], "user@gigadb.org");
       $this->fillField(["id" => "LoginForm_password"], "gigadb");
       $this->click("Login");
       $this->see("John's GigaDB Page");
   }
}
