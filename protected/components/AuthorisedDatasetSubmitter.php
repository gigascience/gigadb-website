<?php
/**
 * DAO class to retrieve submitter email address from cache if the current user is logged in
 *
 * @param ICache cache object
 * @param StoredDatasetSubmitter the DAO for which this is a cache adapter
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class AuthorisedDatasetSubmitter extends yii\base\BaseObject implements DatasetSubmitterInterface
{
	private $_cachedDatasetSubmitter;
	private $_user;

	public function __construct (CWebUser $user, DatasetSubmitterInterface $datasetSubmitter)
	{
		parent::__construct();
		$this->_user = $user;
		$this->_cachedDatasetSubmitter = $datasetSubmitter;
	}

	public function getDatasetID(): int
	{
		return $this->_cachedDatasetSubmitter->getDatasetID();
	}

	public function getDatasetDOI(): string
	{
		return $this->_cachedDatasetSubmitter->getDatasetDOI();
	}

	/**
	 * Retrieve the email address for dataset_id passed through $_cachedDatasetSubmitter if user is logged in
	 *
	 * If user is not logged in (ie: the user is a guest), we return an empty string
	 *
	 * @uses CachedDatasetSubmitter.php
	 * @return string the email address of the submitter of the dataset
	 */
	public function getEmailAddress(): string
	{
		if( false == $this->_user->getIsGuest() ) {
			$emailAddress = $this->_cachedDatasetSubmitter->getEmailAddress();
			return $emailAddress;
		}
		return "";
	}
}
?>