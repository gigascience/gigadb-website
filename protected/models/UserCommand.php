<?php

/**
 * This is the model class for table "user_command".
 *
 * The followings are the available columns in table 'user_command':
 * @property integer $id
 * @property string $action_label
 * @property integer $requester_id
 * @property integer $actioner_id
 * @property integer $actionable_id
 * @property string $request_date
 * @property string $action_date
 * @property string $status
 */
class UserCommand extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'user_command';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('action_label, requester_id, actionable_id, status', 'required'),
            array('requester_id, actioner_id, actionable_id', 'numerical', 'integerOnly'=>true),
            array('action_label, status', 'length', 'max'=>32),
            array('request_date, action_date', 'safe'),
            array('requester_id','unique'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, action_label, requester_id, actioner_id, actionable_id, request_date, action_date, status', 'safe', 'on'=>'search'),
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
            'requester' => array(self::BELONGS_TO, 'User', 'requester_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'action_label' => 'Action Label',
            'requester_id' => 'Requester',
            'actioner_id' => 'Actioner',
            'actionable_id' => 'Actionable',
            'request_date' => 'Request Date',
            'action_date' => 'Action Date',
            'status' => 'Status',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('action_label',$this->action_label,true);
        $criteria->compare('requester_id',$this->requester_id);
        $criteria->compare('actioner_id',$this->actioner_id);
        $criteria->compare('actionable_id',$this->actionable_id);
        $criteria->compare('request_date',$this->request_date,true);
        $criteria->compare('action_date',$this->action_date,true);
        $criteria->compare('status',$this->status,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return UserCommand the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}