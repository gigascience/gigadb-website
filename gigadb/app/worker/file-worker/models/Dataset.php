<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dataset".
 *
 * @property int $id
 * @property int $submitter_id
 * @property int|null $image_id
 * @property int|null $curator_id
 * @property string|null $manuscript_id
 * @property string $identifier
 * @property string $title
 * @property string $description
 * @property int $dataset_size
 * @property string $ftp_site
 * @property string $upload_status
 * @property string|null $excelfile
 * @property string|null $excelfile_md5
 * @property string|null $publication_date
 * @property string|null $modification_date
 * @property int|null $publisher_id
 * @property string|null $token
 * @property string|null $fairnuse
 *
 * @property CurationLog[] $curationLogs
 * @property GigadbUser $submitter
 * @property GigadbUser $curator
 * @property Image $image
 * @property DatasetAttributes[] $datasetAttributes
 * @property DatasetAuthor[] $datasetAuthors
 * @property DatasetFunder[] $datasetFunders
 * @property FunderName[] $funders
 * @property DatasetLog[] $datasetLogs
 * @property DatasetProject[] $datasetProjects
 * @property DatasetSample[] $datasetSamples
 * @property DatasetType[] $datasetTypes
 * @property Experiment[] $experiments
 * @property ExternalLink[] $externalLinks
 * @property File[] $files
 * @property Link[] $links
 * @property Manuscript[] $manuscripts
 * @property Relation[] $relations
 */
class Dataset extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dataset';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['submitter_id', 'identifier', 'title', 'dataset_size', 'ftp_site'], 'required'],
            [['submitter_id', 'image_id', 'curator_id', 'dataset_size', 'publisher_id'], 'default', 'value' => null],
            [['submitter_id', 'image_id', 'curator_id', 'dataset_size', 'publisher_id'], 'integer'],
            [['description'], 'string'],
            [['publication_date', 'modification_date', 'fairnuse'], 'safe'],
            [['manuscript_id', 'excelfile'], 'string', 'max' => 50],
            [['identifier', 'excelfile_md5'], 'string', 'max' => 32],
            [['title'], 'string', 'max' => 300],
            [['ftp_site'], 'string', 'max' => 100],
            [['upload_status'], 'string', 'max' => 45],
            [['token'], 'string', 'max' => 16],
            [['identifier'], 'unique'],
            [['submitter_id'], 'exist', 'skipOnError' => true, 'targetClass' => GigadbUser::className(), 'targetAttribute' => ['submitter_id' => 'id']],
            [['curator_id'], 'exist', 'skipOnError' => true, 'targetClass' => GigadbUser::className(), 'targetAttribute' => ['curator_id' => 'id']],
            [['image_id'], 'exist', 'skipOnError' => true, 'targetClass' => Image::className(), 'targetAttribute' => ['image_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'submitter_id' => 'Submitter ID',
            'image_id' => 'Image ID',
            'curator_id' => 'Curator ID',
            'manuscript_id' => 'Manuscript ID',
            'identifier' => 'Identifier',
            'title' => 'Title',
            'description' => 'Description',
            'dataset_size' => 'Dataset Size',
            'ftp_site' => 'Ftp Site',
            'upload_status' => 'Upload Status',
            'excelfile' => 'Excelfile',
            'excelfile_md5' => 'Excelfile Md5',
            'publication_date' => 'Publication Date',
            'modification_date' => 'Modification Date',
            'publisher_id' => 'Publisher ID',
            'token' => 'Token',
            'fairnuse' => 'Fairnuse',
        ];
    }

    /**
     * Gets query for [[CurationLogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurationLogs()
    {
        return $this->hasMany(CurationLog::className(), ['dataset_id' => 'id']);
    }

    /**
     * Gets query for [[Submitter]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubmitter()
    {
        return $this->hasOne(GigadbUser::className(), ['id' => 'submitter_id']);
    }

    /**
     * Gets query for [[Curator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurator()
    {
        return $this->hasOne(GigadbUser::className(), ['id' => 'curator_id']);
    }

    /**
     * Gets query for [[Image]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImage()
    {
        return $this->hasOne(Image::className(), ['id' => 'image_id']);
    }

    /**
     * Gets query for [[DatasetAttributes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatasetAttributes()
    {
        return $this->hasMany(DatasetAttributes::className(), ['dataset_id' => 'id']);
    }

    /**
     * Gets query for [[DatasetAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatasetAuthors()
    {
        return $this->hasMany(DatasetAuthor::className(), ['dataset_id' => 'id']);
    }

    /**
     * Gets query for [[DatasetFunders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatasetFunders()
    {
        return $this->hasMany(DatasetFunder::className(), ['dataset_id' => 'id']);
    }

    /**
     * Gets query for [[Funders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFunders()
    {
        return $this->hasMany(FunderName::className(), ['id' => 'funder_id'])->viaTable('dataset_funder', ['dataset_id' => 'id']);
    }

    /**
     * Gets query for [[DatasetLogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatasetLogs()
    {
        return $this->hasMany(DatasetLog::className(), ['dataset_id' => 'id']);
    }

    /**
     * Gets query for [[DatasetProjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatasetProjects()
    {
        return $this->hasMany(DatasetProject::className(), ['dataset_id' => 'id']);
    }

    /**
     * Gets query for [[DatasetSamples]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatasetSamples()
    {
        return $this->hasMany(DatasetSample::className(), ['dataset_id' => 'id']);
    }

    /**
     * Gets query for [[DatasetTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatasetTypes()
    {
        return $this->hasMany(DatasetType::className(), ['dataset_id' => 'id']);
    }

    /**
     * Gets query for [[Experiments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExperiments()
    {
        return $this->hasMany(Experiment::className(), ['dataset_id' => 'id']);
    }

    /**
     * Gets query for [[ExternalLinks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExternalLinks()
    {
        return $this->hasMany(ExternalLink::className(), ['dataset_id' => 'id']);
    }

    /**
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(File::className(), ['dataset_id' => 'id']);
    }

    /**
     * Gets query for [[Links]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLinks()
    {
        return $this->hasMany(Link::className(), ['dataset_id' => 'id']);
    }

    /**
     * Gets query for [[Manuscripts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getManuscripts()
    {
        return $this->hasMany(Manuscript::className(), ['dataset_id' => 'id']);
    }

    /**
     * Gets query for [[Relations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRelations()
    {
        return $this->hasMany(Relation::className(), ['dataset_id' => 'id']);
    }
}
