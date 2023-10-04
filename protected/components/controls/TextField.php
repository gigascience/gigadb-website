<?php
Yii::import('application.components.controls.BaseInput');

class TextField extends BaseInput
{
  public function run()
  {
    $this->renderControlGroup(function () {
      echo $this->form->textField($this->model, $this->attributeName, $this->inputOptions);
    });
  }
}
