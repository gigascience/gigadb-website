<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mockup_url".
 *
 * @property int $id
 * @property string|null $url_fragment
 * @property string|null $jwt_token
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class MockupUrl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mockup_url';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url_fragment'], 'string', 'max' => 36],
            [['jwt_token'], 'string', 'max' => 512],
            [['url_fragment'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url_fragment' => 'Url Fragment',
            'jwt_token' => 'JWT Token',
        ];
    }
}
