<?php

/**
 * This is the model class for table "alternative_identifiers".
 *
 * The followings are the available columns in table 'alternative_identifiers':
 * @property integer $id
 * @property integer $sample_id
 * @property integer $extdb_id
 * @property string $extdb_accession
 *
 * The followings are the available model relations:
 * @property Extdb $extdb
 * @property Sample $sample
 */
class AlternativeIdentifiers extends MyActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return AlternativeIdentifiers the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'alternative_identifiers';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('sample_id, extdb_id', 'required'),
            array('sample_id, extdb_id', 'numerical', 'integerOnly'=>true),
            array('extdb_accession', 'length', 'max'=>100),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, sample_id, extdb_id, extdb_accession', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'extdb' => array(self::BELONGS_TO, 'Extdb', 'extdb_id'),
            'sample' => array(self::BELONGS_TO, 'Sample', 'sample_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'sample_id' => 'Sample',
            'extdb_id' => 'Extdb',
            'extdb_accession' => 'Extdb Accession',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('sample_id',$this->sample_id);
        $criteria->compare('extdb_id',$this->extdb_id);
        $criteria->compare('extdb_accession',$this->extdb_accession,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function behaviors() {
        return array(
            'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetRelatedTableBehavior',
        );
    }
}