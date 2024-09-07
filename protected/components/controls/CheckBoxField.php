<?php

/**
 * Example usage:
 *
 *  $this->widget('application.components.controls.CheckBoxField', [
 *    'form' => $form, // required
 *    'model' => $model, // required
 *    'attributeName' => 'is_primary', // required
 *    'groupOptions' => ['class' => 'my-custom-class'], // optional
 *    'checkboxOptions' => ['class' => 'my-checkbox-class'], // optional
 *    'labelOptions' => ['class' => 'my-label-class'], // optional
 *    'errorOptions' => ['class' => 'my-error-class'], // optional
 *  ]);
 */

class CheckBoxField extends CWidget
{
  public $form;
  public $model;
  public $attributeName;
  public $groupOptions;
  public $checkboxOptions;
  public $labelOptions;
  public $errorOptions;
  public $label = null;

  private function hasError()
  {
    return $this->model->hasErrors($this->attributeName);
  }

  private function mergeCssClasses($options, $defaultClass)
  {
    return isset($options['class']) ? "{$defaultClass} {$options['class']}" : $defaultClass;
  }

  public function run()
  {
    $errorId = $this->attributeName . '-error';
    $this->groupOptions['class'] = $this->mergeCssClasses($this->groupOptions, 'form-group checkbox' . ($this->hasError() ? ' has-error' : ''));
    $this->labelOptions['class'] = $this->mergeCssClasses($this->labelOptions, 'control-label');
    $this->errorOptions['class'] = $this->mergeCssClasses($this->errorOptions, 'help-block');
    $this->errorOptions['id'] = $errorId;

    if ($this->hasError()) {
      $this->checkboxOptions['aria-describedby'] = $errorId;
    }

    echo CHtml::openTag('div', $this->groupOptions);
    echo $this->form->checkBox($this->model, $this->attributeName, $this->checkboxOptions);
    if ($this->label) {
        echo CHtml::tag('div', [], CHtml::tag(
            'label',
            [],
            CHtml::encode($this->label)
        ));
    } else {
        echo $this->form->labelEx($this->model, $this->attributeName, $this->labelOptions);
    }
    echo $this->form->error($this->model, $this->attributeName, $this->errorOptions);
    echo CHtml::closeTag('div');
  }
}
