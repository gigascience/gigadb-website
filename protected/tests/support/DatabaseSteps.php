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
	 * to be used with gigadb database
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
	 * to be used with gigadb database
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
	 * to be used with gigadb database
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

	/**
	 * create a filedrop_account record
	 *
	 * to be used with fuw database
	 *
	 * @param PDO $dbh
	 * @param string $doi
	 * @param string $instructions
	 * @return int $id the id of the record just created
	 */
	public function makeFiledropAccountRecord(PDO $dbh, string $doi, string $instructions): int
	{
		$sql = "insert into filedrop_account(doi, instructions, upload_login, upload_token, download_login, download_token, status) values(:doi,:instructions, :upload_login, :upload_token, :download_login, :download_token, 1) returning id";
		$sth = $dbh->prepare($sql);
		$sth->bindValue(':doi', "$doi");
		$sth->bindValue(':instructions', "$instructions");
		$sth->bindValue(':upload_login', "agasniashgadaf");
		$sth->bindValue(':upload_token', "agasniashgadaf");
		$sth->bindValue(':download_login', "agasniashgadaf");
		$sth->bindValue(':download_token', "agasniashgadaf");
		$sth->execute();
		$result = $sth->fetch(PDO::FETCH_OBJ);
		$sth = null;
		return $result->id;
	}


	/**
	 * tear down FUW User account
	 *
	 * to be used with fuw database
	 *
	 * @param PDO $dbh
	 * @param string $email
	 */
	public function tearDownUserIdentity(PDO $dbh, ?string $email): void
	{
		if ($dbh && $email) {
			$sql = "delete from public.user where email=:email";
			$sth = $dbh->prepare($sql);
			$sth->bindValue(":email", "$email");
			$sth->execute();
			$sth = null;
		}
	}

	/**
	 * tear down FUW Filedrop account
	 *
	 * to be used with fuw database
	 *
	 * @param PDO $dbh
	 * @param int $id
	 */
	public function tearDownFiledropAccount(PDO $dbh, int $id = null): void
	{
		if ($id) {
			$sql = "delete from filedrop_account where id=:id";
			$sth = $dbh->prepare($sql);
			$sth->bindValue(":id", "$id");
			$sth->execute();
			$sth = null;
		}
		else {
			$sql = "delete from filedrop_account";
			$sth = $dbh->prepare($sql);
			$sth->execute();
			$sth = null;
		}
	}

	/**
	 * tear down FUW file uploads
	 *
	 * to be used with fuw database
	 *
	 * @param PDO $dbh
	 * @param array $ids
	 */
	public function tearDownFileUploads(PDO $dbh, array $ids = null): void
	{
		if ($ids) {
			$deleteUploadsQuery = "delete from upload where id=:id";
        	$deleteUploadsStatement = $dbh->prepare($deleteUploadsQuery);
	        foreach ($ids as $fileId) {
	            echo PHP_EOL."deleting upload of id {$fileId}".PHP_EOL;
	            $deleteUploadsStatement->bindValue(":id", $fileId);
	            $deleteUploadsStatement->execute();
	        }
	        $deleteUploadsStatement = null;
		}
		else {
			$sql = "delete from upload";
			$sth = $dbh->prepare($sql);
			$sth->execute();
			$sth = null;
		}
	}

	/**
	 * setup FUW filedrop account
	 *
	 * to be used with fuw database
	 *
	 * @param PDO $dbh
	 * @param string $doi
	 *
	 * @return int Id of the just created record
	 */
	public function setUpFiledropAccount(PDO $dbh, string $doi): int{
		$insertAccountQuery = "insert into filedrop_account(doi,status,upload_login,upload_token,download_login,download_token) values($doi,1,'uploader-$doi','sdafad','downloader-$doi','asdgina') returning id";
        $insertAccountStatement = $dbh->prepare($insertAccountQuery);
        $insertAccountStatement->execute();
        $account = $insertAccountStatement->fetch(PDO::FETCH_OBJ);
        $insertAccountStatement = null;
        return $account->id;
	}

	/**
	 * setup FUW file uploads
	 *
	 * to be used with fuw database
	 *
	 * @param PDO $dbh
	 * @param array $files
	 *
	 * @return array Ids of the just created records
	 */
	public function setUpFileUploads(PDO $dbh, array $files): array
	{
		$uploads = [];
		$insertFilesQuery = "insert into upload(doi, name, size, status, location, description, extension, datatype) values(:doi, :name, :size, :status, :location, :description, :extension, :datatype) returning id";
        $insertFilesStatement = $dbh->prepare($insertFilesQuery);
        foreach ($files as $file) {
            $insertFilesStatement->bindValue(':doi',$file['doi']);
            $insertFilesStatement->bindValue(':name',$file['name']);
            $insertFilesStatement->bindValue(':size',$file['size']);
            $insertFilesStatement->bindValue(':status',$file['status']);
            $insertFilesStatement->bindValue(':location',$file['location']);
            $insertFilesStatement->bindValue(':description',$file['description']);
            $insertFilesStatement->bindValue(':extension',$file['extension']);
            $insertFilesStatement->bindValue(':datatype',$file['datatype']);
            $isSuccess = $insertFilesStatement->execute();
            if(!$isSuccess) {
            	echo PHP_EOL."Failure creating in DB file {$file['name']}".PHP_EOL;
            }
            else{
	            $returnId = $insertFilesStatement->fetch(PDO::FETCH_OBJ);
	            $uploads[] = $returnId->id;
            	echo PHP_EOL."Created in DB, file of id {$returnId->id}".PHP_EOL;
            }
        }
        $insertFilesStatement = null;
        return $uploads;
	}
}
?>