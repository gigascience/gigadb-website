<?php

/**
 * This is the model class for table "extdb".
 *
 * The followings are the available columns in table 'extdb':
 * @property integer $id
 * @property string $database_name
 * @property string $definition
 * @property string $database_homepage
 * @property string $database_search_url
 *
 * The followings are the available model relations:
 * @property AlternativeIdentifiers[] $alternativeIdentifiers
 */
class Extdb extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Extdb the static model class
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
        return 'extdb';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('database_name, database_homepage, database_search_url', 'length', 'max'=>100),
            array('definition', 'length', 'max'=>1000),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, database_name, definition, database_homepage, database_search_url', 'safe', 'on'=>'search'),
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
            'alternativeIdentifiers' => array(self::HAS_MANY, 'AlternativeIdentifiers', 'extdb_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'database_name' => 'Database Name',
            'definition' => 'Definition',
            'database_homepage' => 'Database Homepage',
            'database_search_url' => 'Database Search Url',
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
        $criteria->compare('database_name',$this->database_name,true);
        $criteria->compare('definition',$this->definition,true);
        $criteria->compare('database_homepage',$this->database_homepage,true);
        $criteria->compare('database_search_url',$this->database_search_url,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}