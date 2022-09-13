<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "manuscript".
 *
 * @property int $id
 * @property int|null $doi
 * @property string|null $manuscript_number
 * @property string|null $article_title
 * @property string|null $publication_date
 * @property string|null $editorial_status
 * @property string|null $editorial_status_date
 * @property string|null $editors_note
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Manuscript extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'manuscript';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doi', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['doi', 'created_at', 'updated_at'], 'integer'],
            [['publication_date', 'editorial_status_date'], 'date', 'format' => 'dd/MM/yyyy'],
            [['manuscript_number'], 'match', 'pattern' => '/^GIGA\-D\-\d{2}\-\d{5}$/'],
            [['editors_note'], 'string'],
            [['editorial_status'], 'match', 'pattern' => '/^Final\sDecision\s[a-zA-z]+$/'],
            [['manuscript_number', 'article_title', 'editorial_status'], 'string', 'max' => 255],
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
            'manuscript_number' => 'Manuscript Number',
            'article_title' => 'Article Title',
            'publication_date' => 'Publication Date',
            'editorial_status' => 'Editorial Status',
            'editorial_status_date' => 'Editorial Status Date',
            'editors_note' => 'Editors Note',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Factory method to make a new instance of manuscript class using data from the EM report
     *
     * @param array $reportData
     * @return Manuscript[]
     */
    public static function createInstancesFromEmReport(array $reportData): array
    {
        $manuscripts = [];

        foreach ($reportData as $data) {
            $manuscriptReport = new Manuscript();
            $manuscriptReport->manuscript_number = $data['manuscript_number'];
            $manuscriptReport->article_title = $data['article_title'];
            $manuscriptReport->editorial_status_date = $data['editorial_status_date'];
            $manuscriptReport->editorial_status = $data['editorial_status'];
            $manuscripts[] = $manuscriptReport;
        }
        return $manuscripts;
    }
}
