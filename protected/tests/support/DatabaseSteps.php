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
	 * set Up FUW User account
	 *
	 * to be used with fuw database
	 *
	 * @param PDO $dbh
	 * @param string $email
	 * @return id of just created identity
	 */
	public function setUpUserIdentity(PDO $dbh, string $email): void
	{
		if ($dbh && $email) {
			$sql = "insert into public.user (username, auth_key, password_hash, email, created_at, updated_at) values(:username, :auth_key, :password_hash, :email, :created_at, :updated_at)";
			$sth = $dbh->prepare($sql);
			$sth->bindValue(":username", Yii::$app->security->generateRandomString(6));
			$sth->bindValue(":auth_key", Yii::$app->security->generateRandomString(6));
			$sth->bindValue(":password_hash", Yii::$app->security->generateRandomString(6));
			$sth->bindValue(":email", $email);
			$sth->bindValue(":created_at", date("U"));
			$sth->bindValue(":updated_at", date("U"));
			$sth->execute();
			$sth = null;
		}
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
	 * assert for a FUW User account
	 *
	 * to be used with fuw database
	 *
	 * @param PDO $dbh
	 * @param string $email

	 */
	public function assertUserIdentity(PDO $dbh, string $email): void
	{
		if ($dbh && $email) {
			$q = "select count(*) from public.user where email=:email";
			$s = $dbh->prepare($q);
			$s->bindValue(":email", "$email");
			$s->execute();
			if ($s) {
				$this->assertTrue($s->fetchColumn() === 1);
			}
			else {
				$this->fail();
			}
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
	            // echo PHP_EOL."deleting upload of id {$fileId}".PHP_EOL;
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
            	// echo PHP_EOL."Created in DB, file of id {$returnId->id}".PHP_EOL;
            }
        }
        $insertFilesStatement = null;
        return $uploads;
	}

	/**
	 * assert that the datatype and description of a praticular upload match parameters
	 *
	 * @param PDO $dbh
	 * @param int $uploadId database Id of an upload record
	 * @param string $expectedDatatype
	 * @param string $expectedDescription
	 */
	public function assertUploadFields(PDO $dbh, int $uploadId, string $expectedDatatype, string  $expectedDescription)
	{
		$q = "select id, datatype, description from upload where id=:id";
		$s = $dbh->prepare($q);
		$s->bindValue(':id',$uploadId);
		$s->execute();
		$uploadRecord = $s->fetch(PDO::FETCH_OBJ);
		$this->assertEquals($uploadRecord->datatype, $expectedDatatype);
		$this->assertEquals($uploadRecord->description, $expectedDescription);

	}

	/**
	 * Assert that the attributes of a praticular upload match
	 *
	 * @param PDO $dbh
	 * @param int $uploadId database Id of an upload record
	 * @param array $attributes
	 */
	public function assertAttributesForUpload(PDO $dbh, int $uploadId, array $uploadAttributes): void
	{
		$q = "select name, value, unit from attribute where upload_id=:uploadId";
		$s = $dbh->prepare($q);
		$s->bindValue(':uploadId',$uploadId);
		$s->execute();
		$storedAttributes = $s->fetchAll();
		$this->assertCount(count($uploadAttributes), $storedAttributes);
		foreach ($storedAttributes as $attribute) {
			$this->assertNotNull($uploadAttributes[$attribute['name']]);
			$this->assertEquals($attribute['value'], $uploadAttributes[$attribute['name']]["value"]);
			$this->assertEquals($attribute['unit'], $uploadAttributes[$attribute['name']]["unit"]);
		}

	}

	/**
	 * tearDown attributes
	 *
	 * @param PDO $dbh
	 * @param array $uploadIds database Ids of upload records
	 */
	public function tearDownAttributes(PDO $dbh, array $uploadIds): void
	{
		foreach($uploadIds as $uploadId) {
			$q = "delete from attribute where upload_id=:uploadId";
			$s = $dbh->prepare($q);
			$s->bindValue(':uploadId',$uploadId);
			$s->execute();
		}

	}


}
?>