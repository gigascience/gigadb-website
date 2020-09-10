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
			  'email' => strtolower("${firstname}_${lastname}@gigadb.org"),
			  'password' => '5a4f75053077a32e681f81daa8792f95',
			  'first_name' => "$firstname",
			  'last_name' => "$lastname",
			  'role' => 'user',
			  'affiliation' => 'BGI',
			  'is_activated' => true,
			  'newsletter' => false,
			  'previous_newsletter_state' => true,
			  'username' => strtolower("${firstname}_${lastname}"),
			]);
    }

    /**
     * @Given a dataset with DOI :doi owned by user :firstname :lastname has status :status
     */
    public function aDatasetWithDOIOwnedByUserHasStatus($doi, $firstname, $lastname, $status)
    {
    	$submitter_id = $this->grabFromDatabase('gigadb_user', 'id', array('username' => strtolower("${firstname}_${lastname}")));

        $image_id = $this->haveInDatabase('image', [
            "location" => "no_image.jpg",
            "license" => "Public domain",
            "photographer" => "GigaDB",
            "source" => "GigaDB",
            "url" => "http://gigadb.org/images/data/cropped/no_image.png",
        ]);
        $dataset_id = $this->haveInDatabase('dataset', [
			  'submitter_id' => $submitter_id,
			  'identifier' => "$doi",
			  'title' => "Dataset Fantastic",
			  'description' => "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo",
			  'dataset_size' => 3453534634,
			  'ftp_site' => 'ftp://data.org',
			  'upload_status' => "$status",
              'image_id' => $image_id,
			]);

         $this->haveInDatabase('dataset_type', [
              'dataset_id' => $dataset_id,
              'type_id' => 19,
            ]);
    }

    /**
     * @When I browse to the dataset page for :arg1
     *
     * Use amOnUrl instead of amOnPage or instead of relative url 
     * because we might have been on a different domain in previous steps
     * and amOnUrl will set the host to the host part of the url
     */
     public function iBrowseToTheDatasetPageFor($doi)
     {
        $this->amOnUrl("http://gigadb.test/dataset/$doi");
     }

     /**
     * @Then I should be on :arg1
     */
     public function iShouldBeOn($arg1)
     {
        $this->canSeeInCurrentUrl($arg1);
     }
}

