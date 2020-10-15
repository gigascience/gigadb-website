<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sample".
 *
 * @property int $id
 * @property int $species_id
 * @property string $name
 * @property string|null $consent_document
 * @property int|null $submitted_id
 * @property string|null $submission_date
 * @property string|null $contact_author_name
 * @property string|null $contact_author_email
 * @property string|null $sampling_protocol
 *
 * @property AlternativeIdentifiers[] $alternativeIdentifiers
 * @property DatasetSample[] $datasetSamples
 * @property FileSample[] $fileSamples
 * @property GigadbUser $submitted
 * @property Species $species
 * @property SampleExperiment[] $sampleExperiments
 * @property SampleRel[] $sampleRels
 */
class Sample extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sample';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['species_id'], 'required'],
            [['species_id', 'submitted_id'], 'default', 'value' => null],
            [['species_id', 'submitted_id'], 'integer'],
            [['submission_date'], 'safe'],
            [['name', 'contact_author_email', 'sampling_protocol'], 'string', 'max' => 100],
            [['consent_document', 'contact_author_name'], 'string', 'max' => 45],
            [['submitted_id'], 'exist', 'skipOnError' => true, 'targetClass' => GigadbUser::className(), 'targetAttribute' => ['submitted_id' => 'id']],
            [['species_id'], 'exist', 'skipOnError' => true, 'targetClass' => Species::className(), 'targetAttribute' => ['species_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'species_id' => 'Species ID',
            'name' => 'Name',
            'consent_document' => 'Consent Document',
            'submitted_id' => 'Submitted ID',
            'submission_date' => 'Submission Date',
            'contact_author_name' => 'Contact Author Name',
            'contact_author_email' => 'Contact Author Email',
            'sampling_protocol' => 'Sampling Protocol',
        ];
    }

    /**
     * Gets query for [[AlternativeIdentifiers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlternativeIdentifiers()
    {
        return $this->hasMany(AlternativeIdentifiers::className(), ['sample_id' => 'id']);
    }

    /**
     * Gets query for [[DatasetSamples]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatasetSamples()
    {
        return $this->hasMany(DatasetSample::className(), ['sample_id' => 'id']);
    }

    /**
     * Gets query for [[FileSamples]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFileSamples()
    {
        return $this->hasMany(FileSample::className(), ['sample_id' => 'id']);
    }

    /**
     * Gets query for [[Submitted]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubmitted()
    {
        return $this->hasOne(GigadbUser::className(), ['id' => 'submitted_id']);
    }

    /**
     * Gets query for [[Species]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecies()
    {
        return $this->hasOne(Species::className(), ['id' => 'species_id']);
    }

    /**
     * Gets query for [[SampleExperiments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSampleExperiments()
    {
        return $this->hasMany(SampleExperiment::className(), ['sample_id' => 'id']);
    }

    /**
     * Gets query for [[SampleRels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSampleRels()
    {
        return $this->hasMany(SampleRel::className(), ['sample_id' => 'id']);
    }
}
