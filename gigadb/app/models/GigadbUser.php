<?php

namespace GigaDB\models;

/**
 * This is the model class for table "gigadb_user".
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string|null $affiliation
 * @property string $role
 * @property bool $is_activated
 * @property bool $newsletter
 * @property bool $previous_newsletter_state
 * @property string|null $facebook_id
 * @property string|null $twitter_id
 * @property string|null $linkedin_id
 * @property string|null $google_id
 * @property string $username
 * @property string|null $orcid_id
 * @property string|null $preferred_link
 *
 * @property Dataset[] $datasets
 * @property Dataset[] $datasets0
 * @property Sample[] $samples
 * @property Search[] $searches
 */
class GigadbUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gigadb_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password', 'first_name', 'last_name', 'username'], 'required'],
            [['is_activated', 'newsletter', 'previous_newsletter_state'], 'boolean'],
            [['facebook_id', 'twitter_id', 'linkedin_id', 'google_id', 'username', 'orcid_id'], 'string'],
            [['email'], 'string', 'max' => 64],
            [['password', 'preferred_link'], 'string', 'max' => 128],
            [['first_name', 'last_name'], 'string', 'max' => 100],
            [['affiliation'], 'string', 'max' => 200],
            [['role'], 'string', 'max' => 30],
            [['email'], 'unique'],
            [['facebook_id'], 'unique'],
            [['google_id'], 'unique'],
            [['linkedin_id'], 'unique'],
            [['orcid_id'], 'unique'],
            [['twitter_id'], 'unique'],
            [['username'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password' => 'Password',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'affiliation' => 'Affiliation',
            'role' => 'Role',
            'is_activated' => 'Is Activated',
            'newsletter' => 'Newsletter',
            'previous_newsletter_state' => 'Previous Newsletter State',
            'facebook_id' => 'Facebook ID',
            'twitter_id' => 'Twitter ID',
            'linkedin_id' => 'Linkedin ID',
            'google_id' => 'Google ID',
            'username' => 'Username',
            'orcid_id' => 'Orcid ID',
            'preferred_link' => 'Preferred Link',
        ];
    }

    /**
     * Gets query for [[Datasets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatasets()
    {
        return $this->hasMany(Dataset::class, ['submitter_id' => 'id']);
    }

    /**
     * Gets query for [[Datasets0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatasets0()
    {
        return $this->hasMany(Dataset::class, ['curator_id' => 'id']);
    }

    /**
     * Gets query for [[Samples]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSamples()
    {
        return $this->hasMany(Sample::class, ['submitted_id' => 'id']);
    }

    /**
     * Gets query for [[Searches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSearches()
    {
        return $this->hasMany(Search::class, ['user_id' => 'id']);
    }
}
