<?php

/**
 * This is the model class for table "dataset_log".
 *
 * The followings are the available columns in table 'dataset_log':
 * @property integer $id
 * @property integer $dataset_id
 * @property string $message
 * @property string $created_at
 * @property string $model
 * @property string $model_id
 *
 * The followings are the available model relations:
 * @property Dataset $dataset
 */
class CurationLog extends CActiveRecord
{
    public $doi;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DatasetLog the static model class
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
        return 'curation_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('dataset_id', 'required'),
            array('dataset_id', 'numerical', 'integerOnly'=>true),
            array('comments, creation_date, created_by, last_modified_date, last_modified_by, action', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, dataset_id, comments, action, created_by, last_modified_by', 'safe', 'on'=>'search'),
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
            'dataset' => array(self::BELONGS_TO, 'Dataset', 'dataset_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'dataset_id' => 'Dataset',
            'comments' => 'Comments',
            'action' => 'Action',
            'creation_date' => 'Creation Date',
            'created_by' => 'Created By',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
        );
    }

    public static function createLogEntry($id)
    {
        $curationlog = new CurationLog;
        $curationlog->creation_date = date("Y-m-d");
        $curationlog->last_modified_date = null;
        $curationlog->dataset_id = $id;
    }

    public static function createlog($status,$id) {
       
//        $curationlog = new CurationLog;
//        $curationlog->creation_date = date("Y-m-d");
//        $curationlog->last_modified_date = null;
//        $curationlog->dataset_id = $id;
        static::createLogEntry($id);
        $curationlog->created_by = "System";
        $curationlog->action = "Status changed to ".$status;
        if (!$curationlog->save())
            return false;
    }
    
    public static function createlog_assign_curator($id,$creator,$username) {

//        $curationlog = new CurationLog;
//        $curationlog->creation_date = date("Y-m-d");
//        $curationlog->last_modified_date = null;
//        $curationlog->dataset_id = $id;
        static::createLogEntry($id);
        $curationlog->created_by = $creator;
        $curationlog->action = "Curator Assigned"." $username";
        if (!$curationlog->save())
            return false;
    }

    public static function createCurationLogEntry($id)
    {
        $model = Dataset::model()->findByPk($id);
        if ($model->upload_status !== "Published") {
//            $curationlog = new CurationLog;
//            $curationlog->creation_date = date("Y-m-d");
//            $curationlog->last_modified_date = null;
//            $curationlog->dataset_id = $id;
            static::createLogEntry($id);
            $curationlog->created_by = "System";
            $curationlog->action = "Status changed to stuff";
            if (!$curationlog->save())
                return false;
        }
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
        $criteria->with = array('dataset');

        $criteria->compare('id',$this->id);
        $criteria->compare('dataset_id',$this->dataset_id);
        $criteria->compare('comments',$this->comments,true);
        $criteria->compare('action',$this->action,true);
        $criteria->compare('created_by',$this->created_by,true);
        $criteria->compare('last_modified_by',$this->last_modified_by,true);
        $criteria->compare('last_modified_date',$this->last_modified_date,true);
        $criteria->compare('creation_date',$this->creation_date,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
} 
