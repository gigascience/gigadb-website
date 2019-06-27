<?php

namespace backend\models;

use Yii;
use Docker\Docker;
use Docker\API\Model\ContainerSummaryItem;
use Docker\API\Model\{ContainersIdExecPostBody,
                      ExecIdStartPostBody,
                    };

/**
 * This is the model class for table "filedrop_account".
 *
 * @property int $id
 * @property string $doi
 * @property string $upload_login
 * @property string $upload_token
 * @property string $download_login
 * @property string $download_token
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $retired_at
 */
class FiledropAccount extends \yii\db\ActiveRecord
{

    /**
     * non-serializable property so the model filters can handle Docker container manipulation
     *
     * @var \backend\models\DockerManager $dockerManager
     */
    public $dockerManager;

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
            [['created_at', 'updated_at', 'retired_at'], 'safe'],
            [['doi', 'upload_login', 'download_login', 'status'], 'string', 'max' => 100],
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
            'retired_at' => 'Retired At',
        ];
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
     * @return \backend\models\DockerManager $dockerManager
     */
    public function getDockerManager(): \backend\models\DockerManager
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
     * Create a randomly generated string token and write to file
     *
     * @param string $doi dataset identifier
     * @param string $fileName file name where to write token
     * @return bool whether the operation is successful or not
     *
     */
    public function makeToken(string $doi, string $fileName): bool
    {
        $token = self::generateRandomString(16);
        return file_put_contents("/var/private/$doi/".$fileName, $token.PHP_EOL.$token.PHP_EOL) ? true : false ;
    }

    /**
     * Generate a random string
     *
     * @param int $size size of the string
     * @return string generated string
     *
     */
    public static function generateRandomString(int $size): string
    {
        $range = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input_length = strlen($range);
        $random_string = '';
        for($i = 0; $i < $size; $i++) {
            $random_character = $range[random_int(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
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

        $uploaderCommandArray = ["bash","-c","/usr/bin/pure-pw useradd uploader-$doi -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u uploader -d /home/uploader/$doi  < /var/private/$doi/uploader_token.txt"] ;

        $downloaderCommandArray = ["bash","-c","/usr/bin/pure-pw useradd downloader-$doi -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u downloader -d /home/downloader/$doi  < /var/private/$doi/downloader_token.txt"] ;

        $upload_response = $dockerManager->loadAndRunCommand("ftpd", $uploaderCommandArray);
        $download_response = $dockerManager->loadAndRunCommand("ftpd", $downloaderCommandArray);

        if (null === $upload_response || null === $download_response) {
            return false;
        }
        return $status;
    }

    /**
     * prepare directories and generate tokens and assign the data to the model
     *
     * @param string $doi
     */
    public function prepareAccountSetFields(string $doi): bool
    {
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

        return $uploadLogin && $downloadLogin && $uploadToken && $downloadToken ;
    }

    /**
     * method invoked before validation so to call account creation methods
     *
     */
    public function beforeValidate(): bool
    {
        $prepared = $this->prepareAccountSetFields($this->getDOI());
        $ftpd_status = $this->createFTPAccount($this->getDockerManager(), $this->getDOI());
        return $prepared && $ftpd_status;
    }
}
