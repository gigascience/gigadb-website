<?php

Yii::import('application.components.controls.BaseInput');

class TextArea extends BaseInput
{
  public function run()
  {
    $this->renderControlGroup(function () {
      echo $this->form->textArea($this->model, $this->attributeName, $this->inputOptions);
    });
  }
}
