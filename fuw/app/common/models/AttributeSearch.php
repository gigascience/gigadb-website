<?php

namespace common\models;

use yii\base\Model;

/**
 * A search model for Attribute
 *
 *
 * @see https://www.yiiframework.com/doc/guide/2.0/en/rest-resources
 * @see https://www.yiiframework.com/doc/guide/2.0/en/output-data-widgets#filtering-data
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class AttributeSearch extends Model {
	public $id;
    public $name;
    public $value;
    public $unit;
    public $upload_id;


    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'string', 'max' => 255],
            ['value', 'string', 'max' => 255],
            ['unit', 'string', 'max' => 255],
            ['upload_id', 'integer'],
        ];
    }
}
?>