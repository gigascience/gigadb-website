<?php

namespace backend\models;

use Yii;
use Docker\Docker;
use Docker\API\Model\ContainerSummaryItem;

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
     * @var array list of containers that should not be accessed programmatically
     */
    const FORBIDDEN = ["/console_1/","/db_1/"];

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
            [['doi'], 'required'],
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
        $token = $this->generateRandomString(16);
        return file_put_contents("/var/private/$doi/".$fileName, $token.PHP_EOL.$token.PHP_EOL) ? true : false ;
    }

    /**
     * Generate a random string
     *
     * @param int $size size of the string
     * @return string generated string
     *
     */
    private function generateRandomString(int $size): string
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
     * Retrieve a matching container using Docker API
     *
     * @param string regex pattern for the container to match
     * @return null|\Docker\API\Model\ContainerSummaryItem container details
     */
    public function getContainer(string $containerPattern): ?\Docker\API\Model\ContainerSummaryItem
    {
        if( in_array($containerPattern, self::FORBIDDEN) ) {
            return null;
        }

        $docker = Docker::create();
        $containers = $docker->containerList();
        foreach ($containers as $container) {
            if ( preg_match($containerPattern,implode("",$container->getNames())) ) {
                return $container;
            }
        }
        return null;
    }



    /**
     * Create ftp account on the ftpd container using Docker API
     *
     * @param string $doi dataset identifier
     * @return bool if successful return true, otherwise false
     */
    function createFTPAccount(string $doi): bool
    {
        // $status = 0 ;
        // exec("/var/scripts/create_upload_ftp.sh $dataset",$output1, $status);
        // error_log(implode("\n",$output1));
        // exec("/var/scripts/create_download_ftp.sh $dataset",$output2, $status);
        // error_log(implode("\n",$output2));
        // sleep(2);
        // return !$status;

        // 1. get name of ftpd container
        //      getFTPContainerId(): string
        // 2. load exec resource
        //      loadExecResource(string $containerId): string
        // 3. start exec resource
        //      startExecResource(string $execId): DockerRawStream::class
        // 4. check output
        //      checkAccountCreated(DockerRawStream::class $dockerStream): bool
        //
        // see: https://github.com/docker-php/docker-php/blob/master/tests/Resource/ExecResourceTest.php
    }
}
