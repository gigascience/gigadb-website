<?php

/**
 * This is the model class for table "file".
 *
 * The followings are the available columns in table 'file':
 * @property integer $id
 * @property integer $dataset_id
 * @property string $name
 * @property string $location
 * @property string $extension
 * @property string $size
 * @property string $description
 * @property string $date_stamp
 * @property integer $format_id
 * @property integer $type_id
 *
 * The followings are the available model relations:
 * @property Dataset $dataset
 * @property FileFormat $format
 * @property FileType $type
 */
class Folder extends CFormModel
{
    public $dataset_id;
    public $folder_name;
    public $username;
    public $password;
  
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return File the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dataset_id, folder_name,username,password', 'required'),
                    );
			
			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.

	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			
			'dataset_id' => 'Dataset',
			'folder_name' => Yii::t('app' , 'Folder FTP Location'),
                        'username' =>'FTP username',
                        'password' =>'FTP password'
		
		);
	}

	    /**
	 * Convert bytes to human readable format
	 *
	 * @param integer bytes Size in bytes to convert
	 * @return string
	 */
	public function bytesToSize($precision = 2)
	{
		$bytes = $this->size;
	    $kilobyte = 1024;
	    $megabyte = $kilobyte * 1024;
	    $gigabyte = $megabyte * 1024;
	    $terabyte = $gigabyte * 1024;

	    if ($bytes < $megabyte) {
	        return round($bytes / $kilobyte, $precision) . ' KB';
	    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
	        return round($bytes / $megabyte, $precision) . ' MB';

	    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
	        return round($bytes / $gigabyte, $precision) . ' GB';

	    } elseif ($bytes >= $terabyte) {
	        return round($bytes / $terabyte, $precision) . ' TB';
	    } else {
	        return $bytes . ' B';
	    }
	}
	public function getSizeType(){
		$bytes = $this->size;
	    $kilobyte = 1024;
	    $megabyte = $kilobyte * 1024;
	    $gigabyte = $megabyte * 1024;
	    $terabyte = $gigabyte * 1024;

	    if ($bytes < $megabyte) {
	        return 1;
	    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
	        return 2;

	    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
	        return 3;

	    } elseif ($bytes >= $terabyte) {
	        return 4;
	    } else {
	        return 0;
	    }
	}


	public static function staticGetSizeType($bytes){
	    $kilobyte = 1024;
	    $megabyte = $kilobyte * 1024;
	    $gigabyte = $megabyte * 1024;
	    $terabyte = $gigabyte * 1024;

	    if ($bytes < $megabyte) {
	        return 1;
	    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
	        return 2;

	    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
	        return 3;

	    } elseif ($bytes >= $terabyte) {
	        return 4;
	    } else {
	        return 0;
	    }

	}

	public static function staticBytesToSize($bytes,$precision = 2)
	{

	    $kilobyte = 1024;
	    $megabyte = $kilobyte * 1024;
	    $gigabyte = $megabyte * 1024;
	    $terabyte = $gigabyte * 1024;

	    if ($bytes < $megabyte) {
	        return round($bytes / $kilobyte, $precision) . ' KB';
	    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
	        return round($bytes / $megabyte, $precision) . ' MB';

	    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
	        return round($bytes / $gigabyte, $precision) . ' GB';

	    } elseif ($bytes >= $terabyte) {
	        return round($bytes / $terabyte, $precision) . ' TB';
	    } else {
	        return $bytes . ' B';
	    }
	}

	public static function getDatasetIdsByFileIds($fileIds) {
		$criteria = new CDbCriteria();
		$criteria->select='id, dataset_id';
    $criteria->addInCondition('id', $fileIds);
    $criteria->distinct = true;
    $criteria->group = 'id, dataset_id';
  	$files = File::model()->query($criteria,true);
  	$result = CHtml::listData($files,'id','dataset_id');
  	return $result; 
  }
}
