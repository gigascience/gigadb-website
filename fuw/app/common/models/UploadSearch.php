<?php

namespace common\models;

use yii\base\Model;

/**
 * A search model for Upload
 *
 *
 * @see https://www.yiiframework.com/doc/guide/2.0/en/rest-resources
 * @see https://www.yiiframework.com/doc/guide/2.0/en/output-data-widgets#filtering-data
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class UploadSearch extends Model {
	public $id;
    public $doi;
    public $name;
    public $size;
    public $sample_id;
    public $status;
    public $location;
    public $datatype;
    public $extension;
    public $description;
    public $initial_md5;

    public function rules()
    {
        return [
            ['id', 'integer'],
            ['doi', 'string'],
            ['name', 'string', 'max' => 128],
            ['size', 'integer'],
            ['sample_id', 'integer'],
            ['status', 'integer'],
            ['location', 'string', 'max' => 200],
            ['datatype', 'string', 'max' => 32],
            ['extension', 'string','max' => 32],
            ['description', 'string'],
            ['initial_md5', 'string'],
        ];
    }
}
?>