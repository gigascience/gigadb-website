<?php

Yii::import('application.components.controls.BaseInput');

class DropdownField extends BaseInput
{
  public $listDataOptions = [];

  public function run()
  {
    $this->renderControlGroup(function () {
      $data = $this->listDataOptions['data'] ?? [];
      $valueField = $this->listDataOptions['valueField'] ?? 'id';
      $textField = $this->listDataOptions['textField'] ?? 'name';

      echo CHtml::activeDropDownList($this->model, $this->attributeName, CHtml::listData($data, $valueField, $textField), $this->inputOptions);
    });
  }
}
