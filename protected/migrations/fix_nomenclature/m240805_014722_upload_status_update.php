<?php

class m240805_014722_upload_statusUpdate extends CDbMigration
{
	public function safeUp()
	{
        $this->update('dataset', array('upload_status' => 'DataPreparation'), "upload_status = 'DataPending'");
        $this->update('dataset', array('upload_status' => 'UserProvidedData'), "upload_status = 'DataAvailableForReview'");
        $this->update('dataset', array('upload_status' => 'DataAvailableForReview'), "upload_status = 'Submitted'");
	}

	public function safeDown()
	{
        $this->update('dataset', array('upload_status' => 'DataPending'), "upload_status = 'DataPreparation'");
        $this->update('dataset', array('upload_status' => 'DataAvailableForReview'), "upload_status = 'UserProvidedData'");
        $this->update('dataset', array('upload_status' => 'Submitted'), "upload_status = 'DataAvailableForReview'");
	}
}
