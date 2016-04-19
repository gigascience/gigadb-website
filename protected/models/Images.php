<?php

/**
 * This is the model class for table "image".
 * Note: I have to change this Model to Images instead of Image because of this name is conflict with Image.php in the Extension
 *
 * The followings are the available columns in table 'image':
 * @property integer $id
 * @property string $tag
 * @property string $url
 * @property string $license
 * @property string $photographer
 * @property string $source
 *
 * The followings are the available model relations:
 * @property Dataset[] $datasets
 */
class Images extends ImageHaver
{

    public $image_upload;
    public $is_no_image;
    public static $fup_img = '/images/fair.png';
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Image the static model class
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
        return 'image';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('image_upload', 'file', 'types'=>'jpg, gif, png', 'allowEmpty'=>true, 'on'=>'update'),
            array('license, photographer, source', 'required'),
            array('tag', 'length', 'max'=>50),
            array('url, source', 'length', 'max'=>256),
            array('photographer', 'length', 'max'=>128),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, tag, url, license, photographer, source', 'safe', 'on'=>'search'),
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
            'datasets' => array(self::HAS_MANY, 'Dataset', 'image_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'tag' => 'Image Tag',
            'url' => 'Image URL',
            'license' => 'Image License',
            'photographer' => 'Image Photographer',
            'source' => 'Image Source',
            'image_upload' => 'Upload Image',
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
        $criteria->compare('tag',$this->tag,true);
        $criteria->compare('url',$this->url,true);
        $criteria->compare('license',$this->license,true);
        $criteria->compare('photographer',$this->photographer,true);
        $criteria->compare('source',$this->source,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }


    public function save($runValidation = true, $attributes = NULL){
        if (!parent::save()) return false;
        $this->updateImage('image_upload');
        return true;
   }

    public function getImageTypeName() {
        return "image_upload";
    }

#    // Or this, for that matter
#    public function updateImage($type) {
#        if (!isset($_POST["use_$type"]) or $_POST["use_$type"]!= 'current') {
#    	    $image = CUploadedFile::getInstanceByName("{$type}_image");
#	        if ($image !== null) {
#                  $this->setImage($type, $image);
#                  $this->location = $this->image($type);
#            }
#        }
#    }
}
