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
    // TODO replace by prod val before PR, can also use the value directly where it's needed
      return Yii::getAlias('@web') . '/files';   // 'https://' . self::BUCKET
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

  public static function writeLogoFromFile(Filesystem $storage, string $enclosingDirectory, CUploadedFile $file) {

    // get slugified filename from file
    $slugger = new \Symfony\Component\String\Slugger\AsciiSlugger();
    $info = pathinfo($file->getName());
    $fileName = $slugger->slug($info['filename'])->toString();

    // build logo path
    $logoPath = sprintf("%s/%s.%s", $enclosingDirectory, $fileName, $info['extension']);
    $logoUrl = sprintf("%s/%s", Project::getStorageBasePath(), $logoPath);

    Yii::log("writeLogoFromFile: logoPath: " . $logoPath . " logoUrl: " . $logoUrl, "info");

    if ($storage->put(
      $logoPath,
      file_get_contents($file->getTempName()),
      ['visibility' => AdapterInterface::VISIBILITY_PUBLIC]
    )) {
      return $logoUrl;
    }

    return false;
  }

  public function writeLogoFromUrl(Filesystem $storage, string $url) {
    // remove the base path from the url to get the path
    $enclosingDirectory = $this->getLogoPath();
    $filename = basename($url);
    $logoPath = sprintf("%s/%s", $enclosingDirectory, $filename);
    $logoUrl = sprintf("%s/%s", Project::getStorageBasePath(), $logoPath);

    Yii::log("writeLogoFromUrl: logoPath: " . $logoPath . " logoUrl: " . $logoUrl, "info");

    try {
        // Get the source path by removing the storage base path from the url
        $sourcePath = str_replace(Project::getStorageBasePath() . '/', '', $url);
        Yii::log("writeLogoFromUrl: Reading from source path: " . $sourcePath, "info");

        // Read content from storage
        $content = $storage->read($sourcePath);

        if ($content === false) {
            Yii::log("writeLogoFromUrl: Failed to read content from source path", "error");
            return false;
        }

        // Write to new location
        if ($storage->put(
            $logoPath,
            $content,
            ['visibility' => AdapterInterface::VISIBILITY_PUBLIC]
        )) {
            return $logoUrl;
        }
    } catch (\Exception $e) {
        Yii::log("writeLogoFromUrl: Error processing file: " . $e->getMessage(), "error");
        return false;
    }

    return false;
}

  /**
   * Delete an existing logo from storage
   *
   * @param Filesystem $targetStorage
   * @return bool
   */
  public function deleteLogo(Filesystem $targetStorage): bool
  {
      $logoPath = $this->getLogoPath();

      Yii::log("deleteLogo: Attempting to delete logo directory at " . $logoPath, "info");


      try {
          if ($targetStorage->has($logoPath)) {
              if ($targetStorage->deleteDir($logoPath)) {
                  Yii::log("deleteLogo: Successfully deleted logo directory at " . $logoPath, "info");
                  return true;
              }
          }
          Yii::log("deleteLogo: Directory not found at " . $logoPath, "warning");
          // return true to avoid aborting project deletion
          return true;
      } catch (\Exception $e) {
          Yii::log("deleteLogo: Failed to delete logo directory: " . $e->getMessage(), "error");
          // return true to avoid aborting project deletion
          return true;
      }
  }

  public function getLogoPath(): string
  {
    return Yii::$app->params['environment'] . '/images/projects/' . $this->getUuid();
  }

  public static function getTempLogoPath(): string
  {
    return Yii::$app->params['environment'] . '/images/projects/temp/' . Uuid::uuid4()->toString();
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

      Yii::log("Project::beforeDelete: Deleting logo for project " . $this->id, "info");
      return $this->deleteLogo(Yii::$app->cloudStore); // if this returns false, project deletion will be aborted
  }
}
