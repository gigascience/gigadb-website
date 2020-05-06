<?php

namespace backend\models;

use yii\base\Model;

/**
 * A search model for FiledropAccount (to allow filtered lookup on the REST API)
 *
 *
 * @see https://www.yiiframework.com/doc/guide/2.0/en/rest-resources
 * @see https://www.yiiframework.com/doc/guide/2.0/en/output-data-widgets#filtering-data
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class FiledropAccountSearch extends Model {
	public $doi;
	public $status;

	public function rules() {
		return [
			['doi', 'string'],
			['status', 'in', 'range' => [FiledropAccount::STATUS_ACTIVE, FiledropAccount::STATUS_TERMINATED]],
		];
	}
}

?>