<?php

use Behat\Behat\Context\BehatContext;
use Behat\MinkExtension\Context\MinkContext;


/**
 * GigadbWebsiteContext Features context.
 */
class FeatureContext extends Behat\MinkExtension\Context\MinkContext
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

        $this->useContext('affiliate_login', new AffiliateLoginContext($parameters));
        $this->useContext('normal_login', new NormalLoginContext($parameters));

        $this->useContext('dataset_view_context', new DatasetViewContext($parameters));
        $this->useContext('admins_attach_author_user', new AuthorUserContext($parameters));
        $this->useContext('datasets_on_profile', new DatasetsOnProfileContext($parameters));
        $this->useContext('claim_dataset', new ClaimDatasetContext($parameters));
        $this->useContext('merge_authors', new AuthorMergingContext($parameters));
        $this->useContext('GigadbWebsiteContext', new GigadbWebsiteContext($parameters));
        // $this->useContext('minkContext', new Behat\MinkExtension\Context\MinkContext($parameters));
    }

}