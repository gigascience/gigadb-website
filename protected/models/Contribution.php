<?php

/**
 * This is the model class for table "contribution".
 *
 * The followings are the available columns in table 'contribution':
 * @property integer $id
 * @property string $name
 * @property string $source
 * @property string $description
 *
 */
class Contribution extends CActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'contribution';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('name, source, description', 'required'),
            array('name, source, description', 'length', 'max' => 255),
            array('name', 'unique'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'source' => 'Source',
            'description' => 'Description',
        );
    }
}
