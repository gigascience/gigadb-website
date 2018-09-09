<?php

use Behat\Behat\Context\BehatContext;
use Behat\MinkExtension\Context\MinkContext;


/**
 * GigadbWebsiteContext Features context.
 */
class FeatureContext extends BehatContext
{
    private $admin_login;
    private $admin_password;
    private $user_login;
    private $user_password;
    private $time_start;


	public function __construct(array $parameters)
    {

        $this->admin_login = getenv("GIGADB_admin_tester_email");
        $this->admin_password = getenv("GIGADB_admin_tester_password") ;

        $this->useContext('AffiliateLoginContext', new AffiliateLoginContext($parameters));
        $this->useContext('NormalLoginContext', new NormalLoginContext($parameters));
        $this->useContext('DatasetViewContext', new DatasetViewContext($parameters));
        $this->useContext('AuthorUserContext', new AuthorUserContext($parameters));
        $this->useContext('DatasetsOnProfileContext', new DatasetsOnProfileContext($parameters));
        $this->useContext('ClaimDatasetContext', new ClaimDatasetContext($parameters));
        $this->useContext('AuthorMergingContext', new AuthorMergingContext($parameters));
        $this->useContext('GigadbWebsiteContext', new GigadbWebsiteContext($parameters));
        $this->useContext('MinkContext', new Behat\MinkExtension\Context\MinkContext($parameters));
    }

}