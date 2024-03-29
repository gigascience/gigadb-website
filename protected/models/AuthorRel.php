<?php

/**
 * This is the model class for table "author_rel".
 *
 * The followings are the available columns in table 'author_rel':
 * @property integer $id
 * @property integer $author_id
 * @property integer $related_author_id
 * @property integer $relationship_id
 *
 * The followings are the available model relations:
 * @property Relationship $relationship
 * @property Author $author
 */
class AuthorRel extends CActiveRecord
{
	/**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return AuthorRel the static model class
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
        return 'author_rel';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('author_id, related_author_id', 'required'),
            array('author_id, related_author_id, relationship_id', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, author_id, related_author_id, relationship_id', 'safe', 'on'=>'search'),
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
            'relationship' => array(self::BELONGS_TO, 'Relationship', 'relationship_id'),
            'author' => array(self::BELONGS_TO, 'Author', 'author_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'author_id' => 'Author',
            'related_author_id' => 'Related Author',
            'relationship_id' => 'Relationship',
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
        $criteria->compare('author_id',$this->author_id);
        $criteria->compare('related_author_id',$this->related_author_id);
        $criteria->compare('relationship_id',$this->relationship_id);

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

?>