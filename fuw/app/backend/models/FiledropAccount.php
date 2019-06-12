<?php

namespace backend\models;

use Yii;

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
}
