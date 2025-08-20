<?php

declare(strict_types=1);

class Folder extends CFormModel
{
    public ?int $dataset_id     = null;
    public ?string $folder_name = null;
    public ?string $username    = null;
    public ?string $password    = null;

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
            'folder_name' => Yii::t('app', 'Folder FTP Location'),
                        'username' => 'FTP username',
                        'password' => 'FTP password'

        );
    }



    /**
     * @return array<int|string, string>
     */
    public static function getDatasetIdsByFileIds(array $fileIds): array
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'id, dataset_id';
        $criteria->addInCondition('id', $fileIds);
        $criteria->distinct = true;
        $criteria->group = 'id, dataset_id';
        $files = File::model()->findAll($criteria);

        return CHtml::listData($files, 'id', 'dataset_id');
    }
}
