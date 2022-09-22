
<?php

use \creocoder\flysystem\Filesystem;
use League\Flysystem\AdapterInterface;
use Ramsey\Uuid\Uuid;

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
class Image extends CActiveRecord
{
    /** @const int  database id of the generic image (no_image.png) */
    const GENERIC_IMAGE_ID = 0 ;

    /** @const string bucket name when storage is in the cloud  */
    const BUCKET = "assets.gigadb-cdn.net";

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
            array('license, photographer, source', 'required'),
            array('tag', 'length', 'max'=>120),
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
     * write an image to the desired (Flysystem managed) storage mechanism and update url property with the location
     *
     * @param Filesystem $targetStorage
     * @param string $enclosingDirectory
     * @param CUploadedFile $uploadedFile
     * @return bool
     */
    public function write(Filesystem $targetStorage, string $enclosingDirectory, CUploadedFile $uploadedFile): bool
    {
        $imagePath = Yii::$app->params["environment"]."/images/datasets/$enclosingDirectory/".$uploadedFile->getName();
        if ( $targetStorage->put($imagePath, file_get_contents($uploadedFile->getTempName()), [
            'visibility' => AdapterInterface::VISIBILITY_PUBLIC
        ]) ) {
            $this->location = $uploadedFile->getName();
            $this->url = "https://".self::BUCKET."/$imagePath" ;
            return true;
        }
        Yii::log("Error attempting to write image to the storage","error");
        return false;
    }

    /**
     * Method return true if image's url property is valid, false otherwise
     *
     * @return bool
     */
    public function isUrlValid(): bool
    {
        if ( CompatibilityHelper::str_starts_with($this->url,"https://" ) )
            return true;
        return false;
    }

    /**
     * Clear the url property and queue its old value in a new images_todelete record
     *
     * @param object|null $db
     * @return bool
     * @throws CDbException
     * @throws Exception
     */
    public function deleteFile(object $db = null): bool
    {
        $dbConnection = !empty($db) ? $db : $this->getDbConnection();
        $oldUrl = $this->url;
        try {
            if( $this->isUrlValid() ) {
                $inserted = $dbConnection->createCommand()->insert("images_todelete", [
                    "url" => $oldUrl
                ]);
                if ($inserted) {
                    $this->url = null;
                    if ( ! $this->save() )
                        throw new Exception($this->getError());
                }
                return true;
            }
            Yii::log("Failed deleting file for url $oldUrl". "error");

            return false;
        }
        catch (Exception | CDbException $e) {
            Yii::log($e->getMessage(),"error");
            return false;
        }
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if( !empty($this->url))
                return $this->deleteFile();
            return true;
        }
        return false;

    }
}
