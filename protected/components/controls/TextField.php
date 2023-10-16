<?php

/**
 * Example usage:
 *
 * $this->widget('application.components.controls.TextField', array(
 *  'form' => $form,
 *  'model' => $model,
 *  'attributeName' => 'username',
 *  // Optional attributes
 *  'description' => 'Enter your username.',
 *  'inputOptions' => array(
 *      'placeholder' => 'Username',
 *  ),
 *  'labelOptions' => array(
 *      'class' => 'custom-label-class', // Additional class
 *  ),
 * ));
 */

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
