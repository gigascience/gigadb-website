<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;
use Behat\YiiExtension\Context\YiiAwareContextInterface;

/**
 * GigadbWebsiteContext Features context.
 */
class GigadbWebsiteContext extends Behat\MinkExtension\Context\MinkContext implements Behat\YiiExtension\Context\YiiAwareContextInterface
{
    private $admin_login = null;
    private $admin_password = null ;

	public function __construct(array $parameters)
    {

        $this->admin_login = $_ENV["GIGADB_admin_tester_email"];
        $this->admin_password = $_ENV["GIGADB_admin_tester_password"] ;

        $this->useContext('issue64', new AffiliateLoginContext($parameters));
    }


    public function setYiiWebApplication(\CWebApplication $yii)
    {
        $this->yii = $yii ;
    }

    public function getYii()
    {
        if (null === $this->yii) {
            throw new \RuntimeException(
                'Yii instance has not been set on Yii context class. ' .
                'Have you enabled the Yii Extension?'
            );
        }
        return $this->yii ;
    }


    /**
     * @AfterStep
    */
    public function debugStep($event)
    {
        if ($event->getResult() == 4 ) {
            $this->printCurrentUrl();
            try { # take a snapshot of web page
                $content = $this->getSession()->getDriver()->getContent();
                $file_and_path = sprintf('%s_%s_%s',"content", date('U'), uniqid('', true)) ;
                file_put_contents("/tmp/".$file_and_path.".html", $content);
                if (PHP_OS === "Darwin" && PHP_SAPI === "cli") {
                    // exec('open -a "Preview.app" ' . $file_and_path.".png");
                    exec('open -a "Safari.app" ' . $file_and_path.".html");
                }
            }
            catch (Behat\Mink\Exception\DriverException $e) {
                print_r("Unable to take a snatpshot");
            }
        }
    }

     /**
     * @Given /^I sign in as an admin$/
     */
    public function iSignInAsAnAdmin()
    {
         $this->visit("/site/login");
         $this->fillField("LoginForm_username", $this->admin_login);
         $this->fillField("LoginForm_password", $this->admin_password);
         $this->pressButton("Login"); 
    }


}