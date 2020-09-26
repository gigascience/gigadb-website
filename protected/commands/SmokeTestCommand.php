<?php
/**
 * Create and remove smoke test data to allow end to end non-destructive testing of FUW workflow on cloud environments
 *
 * TODO: make the test id a constant
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class SmokeTestCommand extends CConsoleCommand {
	
	public function getHelp() {
		$helpText = "Create and remove smoke test data to allow end to end non-destructive testing of FUW workflow on cloud environments".PHP_EOL;
		$helpText .= "Usage: ./protected/yiic smoketest createdata".PHP_EOL;
		$helpText .= "Usage: ./protected/yiic smoketest removedata".PHP_EOL;
        return $helpText;
    }

    public function actionCreateData($args) {
    	echo "Creating smoke test data...".PHP_EOL;
    	// Create a test author
    	$sql = "insert into gigadb_user(id, email, password, first_name, last_name, affiliation, role, is_activated, newsletter, previous_newsletter_state, facebook_id, twitter_id, linkedin_id, google_id, username, orcid_id, preferred_link) 
values(9999,'gigadb-smoke-test-user@rijam.sent.as','5a4f75053077a32e681f81daa8792f95','Joy','Fox','BGI','user','t','f','t',NULL,NULL,NULL,NULL,'gigadb-smoke-test-user@rijam.sent.as',NULL,'EBI')";

		Yii::app()->db->createCommand($sql)->execute();

		// Create a default image record
		$sql="insert into image(id,location, tag, url, license, photographer, source) values(9999,'no_image.png','no image icon', 'http://gigadb.org/images/data/cropped/no_image.png', 'Public domain','GigaDB','GigaDB')";
		Yii::app()->db->createCommand($sql)->execute();

    	// Create a test dataset
    	$sql = "insert into dataset(id, submitter_id, image_id, identifier, title, dataset_size, ftp_site, upload_status) values(9999,9999,9999, '000007','smoke test',342564,'ftp://','AssigningFTPbox')";
    	Yii::app()->db->createCommand($sql)->execute();

    	return 0;
	}

	public function actionRemoveData($args) {
		echo "Removing smoke test data...".PHP_EOL;
		$sql = "delete from dataset where id=9999";
		Yii::app()->db->createCommand($sql)->execute();
		$sql = "delete from gigadb_user where id=9999";
		Yii::app()->db->createCommand($sql)->execute();
		$sql = "delete from image where id=9999";
		Yii::app()->db->createCommand($sql)->execute();


		system("rm -rf /home/uploader/000007");
		system("rm -rf /home/downloader/000007");
		system("rm -rf /home/credentials/000007");
		return 0;
	}
}

?>