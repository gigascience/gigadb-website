<?php

namespace backend\models;

use Yii;
use Docker\Docker;
use Docker\API\Model\ContainerSummaryItem;
use Docker\API\Model\{ContainersIdExecPostBody,
                      ExecIdStartPostBody,
                    };

use common\models\Upload;

/**
 * Model that represent a Filedrop account
 *
 * Technically, it's a model class for table "filedrop_account".
 * It needs to interact with the filesystem (create/delete directories),
 * and with Docker daemon (create/delete account in the ftp container)
 * It has before-validate validation hook that trigger creation of directories and ftp accounts
 * when instantiated and saved. The same hook will delete directories and ftp accounts
 * if the model'status is updated to self::STATUS_TERMINATED
 *
 * @property int $id
 * @property string $doi
 * @property string $upload_login
 * @property string $upload_token
 * @property string $download_login
 * @property string $download_token
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $terminated_at
 * @property string $instructions
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FiledropAccount extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_TERMINATED = 0;

    /**
     * non-serializable property so the model filters can handle Docker container manipulation
     *
     * @var \backend\models\DockerManager $dockerManager
     */
    public $dockerManager;

    /**
     * @var bool indicate whether to simulate or actually perform resource altering actions
     */
    public $dryRunMode;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'filedrop_account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doi','upload_login','upload_token','download_login','download_token'], 'required'],
            [['created_at', 'updated_at', 'terminated_at'], 'safe'],
            [['doi', 'upload_login', 'download_login'], 'string', 'max' => 100],
            ['instructions', 'string', 'min' => 3],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_TERMINATED]],
            [['upload_token', 'download_token'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'doi' => 'Doi',
            'upload_login' => 'Upload Login',
            'upload_token' => 'Upload Token',
            'download_login' => 'Download Login',
            'download_token' => 'Download Token',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'terminated_at' => 'Retired At',
            'instructions' => 'Instructions',
        ];
    }

    /**
     * init(), called by Yii2. Here to initialise variable a DockerManager instance
     *
     */
    public function init()
    {
        if (null === $this->getDockerManager()) {
            $this->setDockerManager(new DockerManager());
        }
    }

    /**
     * Initialise a singleton Docker Manager instance for all instances of FiledropAccount
     *
     * @param \backend\models\DockerManager $dockerManager
     */
    public function setDockerManager(\backend\models\DockerManager $dockerManager): void
    {
        $this->dockerManager = $dockerManager;
    }

    /**
     * Initialise a singleton Docker Manager instance for all instances of FiledropAccount
     *
     * @return null|\backend\models\DockerManager $dockerManager
     */
    public function getDockerManager(): ?\backend\models\DockerManager
    {
        return $this->dockerManager;
    }

    /**
     * set the doi attribute
     *
     * @param string $doi DOI
     */
    public function setDOI(string $doi): void
    {
        $this->doi = $doi ;
    }

   /**
     * get the doi
     *
     * @return string $doi DOI
     */
    public function getDOI(): string
    {
        return $this->doi ;
    }

    /**
     * set the status attribute
     *
     * @param string $status status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status ;
    }

   /**
     * get the status
     *
     * @return string $status status
     */
    public function getStatus(): int
    {
        return $this->status ;
    }

    /**
     * Create directories required for file upload pipeline
     *
     * @param string $doi dataset identifier for which to create directory
     * @return bool whether or not the operation is successful
     */
    function createDirectories(string $doi): bool
    {
        return mkdir("/var/incoming/ftp/$doi", 0770)
                && chmod("/var/incoming/ftp/$doi", 0770)//to bypass umask
                && mkdir("/var/repo/$doi", 0755)
                && mkdir("/var/private/$doi", 0750);

    }

    /**
     * Remove directories required for suspending a filedrop account
     *
     * @param string $doi dataset identifier for which to remove directory
     * @return bool whether or not the operation is successful
     */
    function removeDirectories(string $doi): bool
    {
        if ( Yii::$app->fs->has("incoming/ftp/$doi") ) {
            if ( ! Yii::$app->fs->deleteDir("incoming/ftp/$doi") ) {
                return false;
            }
        }

        if ( Yii::$app->fs->has("repo/$doi") ) {
            if ( ! Yii::$app->fs->deleteDir("repo/$doi") ) {
                return false;
            }
        }

        if ( Yii::$app->fs->has("private/$doi") ) {
            if ( ! Yii::$app->fs->deleteDir("private/$doi") ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create a randomly generated string token and write to file
     *
     * @param string $doi dataset identifier
     * @param string $fileName file name where to write token
     * @return bool whether the operation is successful or not
     *
     */
    public function makeToken(string $doi, string $fileName): bool
    {
        $token = Yii::$app->security->generateRandomString(16);
        return Yii::$app->fs->put(
                        "private/$doi/".$fileName,
                        $token.PHP_EOL.$token.PHP_EOL
                    );
    }

    /**
     * Create ftp account on the ftpd container using Docker API
     *
     * @param \backend\models\DockerManager $dockerManager instance of docker API
     * @param string $accountType type of account ("uploader" or "downloader")
     * @param string $doi dataset identifier
     * @return bool if successful return true, otherwise false
     */
    function createFTPAccount(\backend\models\DockerManager $dockerManager, string $doi): bool
    {
        $status = true ;

        $dryRunModeArray = ["bash","-c","pwd"] ;
        $uploaderCommandArray = ["bash","-c","/usr/bin/pure-pw useradd uploader-$doi -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u uploader -d /home/uploader/$doi  < /var/private/$doi/uploader_token.txt"] ;

        $downloaderCommandArray = ["bash","-c","/usr/bin/pure-pw useradd downloader-$doi -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u downloader -d /home/downloader/$doi  < /var/private/$doi/downloader_token.txt"] ;


        if ($this->dryRunMode) {
            $dryRunModeRsp = $dockerManager->loadAndRunCommand("ftpd", $dryRunModeArray);
        }
        else {
            $upload_response = $dockerManager->loadAndRunCommand("ftpd", $uploaderCommandArray);
            $download_response = $dockerManager->loadAndRunCommand("ftpd", $downloaderCommandArray);
        }

        if (null === $upload_response || null === $download_response) {
            return false;
        }
        return $status;
    }

    /**
     * Remove ftp account on the ftpd container using Docker API
     *
     * @param \backend\models\DockerManager $dockerManager instance of docker API
     * @param string $accountType type of account ("uploader" or "downloader")
     * @param string $doi dataset identifier
     * @return bool if successful return true, otherwise false
     */
    function removeFTPAccount(\backend\models\DockerManager $dockerManager, string $doi): bool
    {
        $status = true;

        $uploaderCommandArray = ["bash","-c","/usr/bin/pure-pw userdel uploader-$doi -f /etc/pure-ftpd/passwd/pureftpd.passwd -m"] ;

        $downloaderCommandArray = ["bash","-c","/usr/bin/pure-pw userdel downloader-$doi -f /etc/pure-ftpd/passwd/pureftpd.passwd -m"] ;

        $upload_response = $dockerManager->loadAndRunCommand("ftpd", $uploaderCommandArray);
        $download_response = $dockerManager->loadAndRunCommand("ftpd", $downloaderCommandArray);

        if (null === $upload_response || null === $download_response) {
            return false;
        }
        return $status;
    }

    /**
     * return related uploads objects
     */
    public function getUploads()
    {
        return $this->hasMany(Upload::className(), ['filedrop_account_id' => 'id']);
    }

    /**
     * remove uploads associated with this FiledropAccount object by marking them as archived
     *
     * @return int number of uploads successfully archived
     */
    public function removeUploads(): ?int
    {
        $nbArchived = Upload::updateAll(['status' => "archived"], "doi = '{$this->getDOI()}'");
        return $nbArchived;
    }

    /**
     * check ftp account exists the ftpd container using Docker API
     *
     * @param \backend\models\DockerManager $dockerManager instance of docker API
     * @param string $accountType type of account ("uploader" or "downloader")
     * @param string $doi dataset identifier
     * @return string if exists return true, otherwise false
     */
    function checkFTPAccount(\backend\models\DockerManager $dockerManager, string $doi): string
    {


        $command = ["bash","-c","cat /etc/pure-ftpd/passwd/pureftpd.passwd | grep $doi"] ;

        $stream = $dockerManager->loadAndRunCommand("ftpd", $command);


        $response = '';
        $stream->onStdout(function ($stdout) use (&$response): void {
            $response .= $response;
        });

        $stream->wait();

        return $response;
    }

    /**
     * prepare directories and generate tokens and assign the data to the model
     *
     * @param string $doi
     */
    public function prepareAccountSetFields(string $doi): bool
    {


        if (!$this->dryRunMode) {
            // create directories
            $this->createDirectories("$doi");
            // create tokens
            $result1 = $this->makeToken("$doi",'uploader_token.txt');
            $result1 = $this->makeToken("$doi",'downloader_token.txt');
            // derive logins and tokens
            $uploadLogin = "uploader-$doi";
            $uploadToken = rtrim(file("/var/private/$doi/uploader_token.txt")[0]);

            $downloadLogin = "downloader-$doi";
            $downloadToken = rtrim(file("/var/private/$doi/downloader_token.txt")[0]);
            $this->upload_login = $uploadLogin ;
            $this->upload_token = $uploadToken ;

            $this->download_login = $downloadLogin ;
            $this->download_token = $downloadToken ;
        }
        else {
            Yii::warning("Creating directory in /var/incoming/ftp/ [dry-run]");
            mkdir("/var/incoming/ftp/dryRunMode", 0770, true);
            rmdir("/var/incoming/ftp/dryRunMode");
            Yii::warning("Creating directory in /var/repo/ [dry-run]");
            mkdir("/var/repo/dryRunMode", 0755);
            rmdir("/var/repo/dryRunMode");
            Yii::warning("Creating directory in /var/private/ [dry-run]");
            mkdir("/var/private/dryRunMode", 0750);
            rmdir("/var/private/dryRunMode");
        }


        return $this->upload_login && $this->upload_token && $this->download_login && $this->download_token ;
    }

    /**
     * method invoked before validation so to call account creation methods
     *
     */
    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            if ( $this->getIsNewRecord() ) {
                $prepared = $this->prepareAccountSetFields($this->getDOI());
                $ftpd_status = $prepared && $this->createFTPAccount($this->getDockerManager(),
                                                                    $this->getDOI());
                if ($prepared && $ftpd_status) {
                    $this->setStatus(self::STATUS_ACTIVE);
                    return true;
                }
                return false;
            }
            else if ( self::STATUS_TERMINATED === $this->getStatus() ) {
                $directoryRemoved = $this->removeDirectories($this->getDOI());
                $directoryAndFTPremoved = $directoryRemoved && $this->removeFTPAccount(
                    $this->getDockerManager(),
                    $this->getDOI()
                );
                if ($directoryAndFTPremoved) {
                    $this->removeUploads();
                }
                return $directoryAndFTPremoved ;
            }
            return true;
        }
        Yii::error('parent::beforeValidate() returns false');
        return false;
    }

    /**
     * prevent save if dry-run mode is on
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ( $this->dryRunMode ) {
            return false;
        }

        return true;
    }
}

