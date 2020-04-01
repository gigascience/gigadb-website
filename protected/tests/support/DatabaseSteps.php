<?php

use Ramsey\Uuid\Uuid;
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
		$q = "select id, datatype, description from upload where id=:id and status = 0";
		$s = $dbh->prepare($q);
		$s->bindValue(':id',$uploadId);
		$s->execute();
		$uploadRecord = $s->fetch(PDO::FETCH_OBJ);
		$this->assertEquals($expectedDatatype, $uploadRecord->datatype);
		$this->assertEquals($expectedDescription, $uploadRecord->description);

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
	 * setUp attributes
	 *
	 * @param PDO $dbh
	 * @param int $uploadId database Id of an upload record
	 * @return string attribute's name
	 */
	public function setUpAttributes(PDO $dbh, int $uploadId): string
	{

		$sql = "insert into public.attribute(name, value, unit, upload_id) values(:name, :value,:unit, :upload_id) returning name";
		$sth = $dbh->prepare($sql);
		$sth->bindValue(":name", Yii::$app->security->generateRandomString(6));
		$sth->bindValue(":value", Yii::$app->security->generateRandomString(6));
		$sth->bindValue(":unit", Yii::$app->security->generateRandomString(6));
		$sth->bindValue(":upload_id", $uploadId);
		$sth->execute();
		$attr = $sth->fetch(PDO::FETCH_OBJ);
        $sth = null;
        return $attr->name;
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

	/**
	 * setUp mockupUrl
	 *
	 * @param PDO $dbh
	 * @param string $email reviewer's email
	 * @param int $validity months of validity for the token
	 * @param string $doi DOI of the dataset associated with the mockup
	 * @return string url_fragment
	 */
	public function setUpMockupUrl(PDO $dbh, string $email, int $validity, string $doi): array
	{

		$tokenSrv = new TokenService([
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'dt' => new DateTime(),
                                ]);

	    $token = $tokenSrv->generateTokenForMockup($email,$validity,$doi);
	    $uuid = Uuid::uuid4();

	    $sql = "insert into public.mockup_url(url_fragment, jwt_token) values(:url_fragment, :jwt_token) returning id, url_fragment";
		$sth = $dbh->prepare($sql);
		$sth->bindValue(":url_fragment", $uuid->toString());
		$sth->bindValue(":jwt_token", $token);
		$sth->execute();
		$mockupUrl = $sth->fetch(PDO::FETCH_OBJ);

        return [$mockupUrl->id, $mockupUrl->url_fragment];
	}


	/**
	 * tearDown mockupUrl
	 *
	 * @param PDO $dbh
	 * @param string $url_fragment uuid of the mockup_url record
	 */
	public function tearDownMockupUrl(PDO $dbh, string $url_fragment): void
	{
		$q = "delete from mockup_url where url_fragment=:url_fragment";
		$s = $dbh->prepare($q);
		$s->bindValue(':url_fragment',$url_fragment);
		$s->execute();
	}

}
?>