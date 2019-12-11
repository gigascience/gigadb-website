<?php

/**
 * browser automation steps to setup test users
 *
 * This trait is to be used in functional tests
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
trait DatabaseSteps
{
	/**
	 * Set up a new user in the database
	 *
	 * @param PDO $dbh
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $role
	 * @param string $email
	 */
	public function setUpUsers(PDO $dbh, string $firstname, string $lastname, string $role, string $email): void
	{
		$sql = "insert into gigadb_user(id, email, password, first_name, last_name, affiliation, role, is_activated, newsletter, previous_newsletter_state, facebook_id, twitter_id, linkedin_id, google_id, username, orcid_id, preferred_link) 
values(681,'$email','5a4f75053077a32e681f81daa8792f95','$firstname','$lastname','BGI','$role','t','f','t',NULL,NULL,NULL,NULL,'$email',NULL,'EBI');";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$sth = null;
	}

	/**
	 * tear down user in the database
	 *
	 * @param PDO $dbh
	 * @param string $email
	 */
	public function tearDownUsers(PDO $dbh, string $email): void
	{
		$sql = "delete from gigadb_user where email='$email'";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$sth = null;
	}


	/**
	 * Set dataset status for test
	 *
	 * @param PDO $dbh
	 * @param string $doi
	 * @param string $status
	 */
	public function setUpDatasetUploadStatus(PDO $dbh, string $doi, string $status): void
	{
		$sql = "update dataset set upload_status='$status' where identifier='$doi'";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$sth = null;
	}

}
?>