<?php

/**
 * Usage examples:
 *
 * With dataset prop:
 *
 * 	$this->widget('application.components.controls.DropdownField', [
 *		'form' => $form,
 *		'model' => $model,
 *		'attributeName' => 'custom_dataset',
 *		'dataset' => $datasets,
 *		'inputOptions' => [
 *			'required' => true,
 *		],
 *	]);
 *
 * With listDataOptions prop:
 *
 *  $this->widget('application.components.controls.DropdownField', [
 *    'form' => $form,
 *    'model' => $model,
 *    'attributeName' => 'dataset_id',
 *    'listDataOptions' => [
 *        'data' => Util::getDois(),
 *        'valueField' => 'id',
 *        'textField' => 'identifier',
 *    ],
 *    'inputOptions' => [
 *        'required' => true,
 *    ]
 *]);
 */

Yii::import('application.components.controls.BaseInput');

class DropdownField extends BaseInput
{
  public $listDataOptions = [];
  public $dataset = [];

  public function run()
  {
    $this->renderControlGroup(function () {
      if ($this->dataset) {
        $dataset = $this->dataset;
      } else {
        $data = $this->listDataOptions['data'] ?? [];
        $valueField = $this->listDataOptions['valueField'] ?? 'id';
        $textField = $this->listDataOptions['textField'] ?? 'name';
        $dataset = CHtml::listData($data, $valueField, $textField);
      }

      echo CHtml::activeDropDownList($this->model, $this->attributeName, $dataset, $this->inputOptions);
    });
  }
}
