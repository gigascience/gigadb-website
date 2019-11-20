<?php
namespace common\tests;

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
    * Define custom actions common to multiple roles here
    */

   /**
     * @Given there is a user :firstname :lastname
     */
   	public function thereIsAUser($firstname, $lastname)
     {
        $this->haveInDatabase('gigadb_user', [
			  'email' => "${firstname}_${lastname}@gigadb.org",
			  'password' => 'foobar',
			  'first_name' => "$firstname",
			  'last_name' => "$lastname",
			  'role' => 'user',
			  'is_activated' => true,
			  'newsletter' => false,
			  'previous_newsletter_state' => false,
			  'username' => "${firstname}_${lastname}",
			]);
    }

    /**
     * @Given a dataset with DOI :doi owned by user :firstname :lastname has status :status
     */
    public function aDatasetWithDOIOwnedByUserHasStatus($doi, $firstname, $lastname, $status)
    {
    	$submitter_id = $this->grabFromDatabase('gigadb_user', 'id', array('username' => "${firstname}_${lastname}"));
         $this->haveInDatabase('dataset', [
			  'submitter_id' => $submitter_id,
			  'identifier' => "$doi",
			  'title' => "Dataset Fantastic",
			  'description' => "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo",
			  'dataset_size' => 3453534634,
			  'ftp_site' => 'ftp://data.org',
			  'upload_status' => "$status",
			]);
    }

     /**
     * @Then I should be on :arg1
     */
     public function iShouldBeOn($arg1)
     {
        $this->canSeeInCurrentUrl($arg1);
     }
}
