<?php
use Behat\Behat\Context\BehatContext;
class DatasetsOnProfileContext extends BehatContext
{
    public function __construct(array $parameters)
    {
    }

	/**
     * @Given /^I am linked to author "([^"]*)"$/
    */
    public function iAmLinkedToAuthor($author)
    {
        if ("Zhang, G" == $author) {
        	exec("vagrant ssh -c \"sudo -Hiu postgres /usr/bin/psql gigadb < /vagrant/sql/author_z_user_link.sql\"",$output);
        }
        else if("Yue, Z" == $author) {
        	exec("vagrant ssh -c \"sudo -Hiu postgres /usr/bin/psql gigadb < /vagrant/sql/author_y_user_link.sql\"",$output);
        }
    }


}