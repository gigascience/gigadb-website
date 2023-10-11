<?php
Yii::import('application.components.controls.BaseInput');

class PasswordField extends BaseInput
{
  public function run()
  {
    $this->renderControlGroup(function () {
      echo $this->form->passwordField($this->model, $this->attributeName, $this->inputOptions);
    });
  }
}
