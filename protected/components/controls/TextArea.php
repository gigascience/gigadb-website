<?php


/**
 * Usage:
 *
 * Full example:
 *  $this->widget('application.components.controls.TextArea', [
 *    'form' => $form, // required
 *    'model' => $model, // required
 *    'attributeName' => 'description', // required
 *    'description' => 'This is a description field.', // optional
 *    'inputOptions' => [
 *      'class' => 'my-input-class',
 *      'required' => true, // this will set aria-required to true
 *      'rows' => 6,
 *      'cols' => 50
 *    ], // optional
 *    'labelOptions' => ['class' => 'my-label-class'], // optional
 *    'errorOptions' => ['class' => 'my-error-class'], // optional
 *    'groupOptions' => ['class' => 'my-group-class'], // optional
 *    'inputWrapperOptions' => ['class' => 'my-wrapper-class'] // optional, used mainly in conjunction with form-horizontal
 *  ]);
 */

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
