<?php

use \creocoder\flysystem\Filesystem;
use League\Flysystem\AdapterInterface;
use Ramsey\Uuid\Uuid;

/**
 * This is the model class for table "project".
 *
 * The followings are the available columns in table 'project':
 * @property integer $id
 * @property integer $dataset_id
 * @property string $url
 * @property string $name
 * @property string $image_location
 *
 * The followings are the available model relations:
 * @property Dataset $dataset
 */
class Project extends CActiveRecord
{

  /** @const string bucket name when storage is in the cloud  */
  const BUCKET = "assets.gigadb-cdn.net";
  const NAMESPACE = "http://gigadb.org/namespaces/project";
  public $image;
  public $image_logo;

  public static function getStorageBasePath()
  {
      // return Yii::getAlias('@web') . '/files'; // testing locally
      return 'https://' . self::BUCKET;
  }

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Project the static model class
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
		return 'project';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('url', 'required'),
      array('url', 'url','message'=>'Please check the URL format'),
			array('url', 'length', 'max'=>128),
			array('name', 'length', 'max'=>255),
			array('image_location', 'length', 'max'=>255),
      array('url','check_duplicate'),
      // array('image', 'file', 'types' => 'jpg, jpeg, png', 'allowEmpty' => true),
      array('image_logo', 'file', 'types' => 'jpg, jpeg, png', 'allowEmpty' => true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, url, name, image_location', 'safe', 'on'=>'search'),
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
			'datasets' => array(self::MANY_MANY, 'Dataset', 'dataset_project(project_id , dataset_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'url' => 'Url',
			'name' => 'Name',
			'image_location' => 'Image Location',
      'image_logo' => 'Image Logo',
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
		$criteria->compare('LOWER(url)',strtolower($this->url),true);
		$criteria->compare('LOWER(name)',strtolower($this->name),true);
		$criteria->compare('LOWER(image_location)',strtolower($this->image_location),true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function getListProjects(){
      $models=Species::model()->findAll();
      $list=array();
      foreach (array_values($models) as $model){
          $list[$model->id] = $model->common_name;
      }
      return $list;
  }

  function check_duplicate(){


      $db_url= Project::model()->findBySql("select name from project where url='$this->url'");

      if($db_url !=null){
      $this->addError('url','Duplicate URL');}

      $db_name= Project::model()->findBySql("select url from project where name='$this->name'");

      if($db_name !=null){
      $this->addError('name','Duplicate Project Name');}


  }

  /**
   * Write a logo file to the server's /tmp directory from an uploaded file
   *
   * @param CUploadedFile $file The uploaded file to write
   * @return string|false The path of the written temp file, or false if write failed
   */
  public static function writeTmpLogoFromFile(CUploadedFile $file) {
      $slugger = new \Symfony\Component\String\Slugger\AsciiSlugger();
      $info = pathinfo($file->getName());
      $fileName = $slugger->slug($info['filename'])->toString();
      $filepath = sprintf('/datasetfiles/%s/%s.%s',
        Uuid::uuid4()->toString(),
        $fileName,
        $info['extension']
      );
      $fullTmpPath = Yii::getAlias('@webroot') . $filepath;

      if (!is_dir($dir = dirname($fullTmpPath)) && !mkdir($dir, 0755, true)) {
          Yii::log("Failed to create directory: " . $dir, 'error');
          return false;
      }

      if (move_uploaded_file($file->getTempName(), $fullTmpPath)) {
          return $filepath;
      }

      Yii::log("Failed to move uploaded file to: " . $fullTmpPath, 'error');
      return false;
  }

  /**
   * Write a logo file to storage from an uploaded file
   *
   * @param Filesystem $storage The storage filesystem to write to
   * @param string $enclosingDirectory The directory path to store the logo in
   * @param CUploadedFile $file The uploaded file to write
   * @return string|false The URL of the written logo file, or false if write failed
   */
  public static function writeLogoFromFile(Filesystem $storage, string $enclosingDirectory, CUploadedFile $file) {
      $slugger = new \Symfony\Component\String\Slugger\AsciiSlugger();
      $info = pathinfo($file->getName());
      $fileName = $slugger->slug($info['filename'])->toString();
      $logoPath = sprintf("%s/%s.%s", $enclosingDirectory, $fileName, $info['extension']);
      $logoUrl = sprintf("%s/%s", Project::getStorageBasePath(), $logoPath);

      if ($storage->put(
        $logoPath,
        file_get_contents($file->getTempName()),
        ['visibility' => AdapterInterface::VISIBILITY_PUBLIC]
      )) {
        return $logoUrl;
      }

      return false;
  }

  /**
   * Write a logo file to storage from a URL
   *
   * @param Filesystem $storage The storage filesystem to write to
   * @param string $url The URL of the source logo file
   * @return string|false The URL of the written logo file, or false if write failed
   */
  public function writeLogoFromUrl(Filesystem $storage, string $url) {
      $enclosingDirectory = $this->getLogoPath();
      $sourcePath = Yii::getAlias('@webroot') . $url;
      $outputPath = sprintf("%s/%s", $enclosingDirectory, basename($url));

      if (!file_exists($sourcePath)) {
          Yii::log("Failed to read content from source path: " . $sourcePath, 'error');
          return false;
      }

      if ($storage->put(
          $outputPath,
          file_get_contents($sourcePath),
          ['visibility' => AdapterInterface::VISIBILITY_PUBLIC]
      )) {
          return sprintf("%s/%s", Project::getStorageBasePath(), $outputPath);
      }

      Yii::log("Failed to write logo to storage at: " . $outputPath, 'error');
      return false;
  }

  /**
   * Delete an existing logo path from storage
   *
   * @param Filesystem $targetStorage
   * @return bool
   */
  public function deleteLogo(Filesystem $targetStorage): bool
  {
      $logoPath = $this->getLogoPath();

      if ($targetStorage->has($logoPath)) {
          $targetStorage->deleteDir($logoPath);
      }

      // return true always to avoid aborting project deletion
      return true;
  }

  public function getLogoPath(): string
  {
      return Yii::$app->params['environment'] . '/images/projects/' . $this->getUuid();
  }

  /**
   * Return a UUID based on the project id
   *
   * @return string
   */
  public function getUuid()
  {
      $uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, self::NAMESPACE."/id/".$this->id);
      return $uuid;
  }

  // this should ensure that logo file is cleaned up before project deletion
  protected function beforeDelete()
  {
      if (!parent::beforeDelete()) {
          return false;
      }

      return $this->deleteLogo(Yii::$app->cloudStore); // if this returns false, project deletion will be aborted
  }
}
