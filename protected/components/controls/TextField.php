<?php

/**
 * Usage:
 *
 * Full example:
 *  $this->widget('application.components.controls.TextField', [
 *    'form' => $form, // required
 *    'model' => $model, // required
 *    'attributeName' => 'username', // required
 *    'description' => 'This is a username field.', // optional
 *    'inputOptions' => [
 *      'class' => 'my-input-class',
 *      'placeholder' => 'Enter your username',
 *      'required' => true, // this will set aria-required to true
 *    ], // optional
 *    'labelOptions' => ['class' => 'my-label-class'], // optional
 *    'errorOptions' => ['class' => 'my-error-class'], // optional
 *    'groupOptions' => ['class' => 'my-group-class'], // optional
 *    'inputWrapperOptions' => ['class' => 'my-wrapper-class'] // optional, used mainly in conjunction with form-horizontal
 *  ]);
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
