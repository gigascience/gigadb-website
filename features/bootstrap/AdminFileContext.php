<?php
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 *
 * @uses GigadbWebsiteContext For loading production like data
 * @uses GigadbWebsiteContext::iSignInAsAnAdmin For signing in as a admin
 * @uses GigadbWebsiteContext::iSignInAsAUser For signing in as a user
 * @uses \Behat\MinkExtension\Context\MinkContext For controlling the web browser
 * @uses \PHPUnit_Framework_Assert
 */

class AdminFileContext implements Context
{

}