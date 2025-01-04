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
  public $isHorizontal = false;
  public $description = null;

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

    if ($this->isHorizontal) {
      $this->groupOptions['class'] = $this->mergeCssClasses($this->groupOptions, 'form-group checkbox-horizontal' . ($this->hasError() ? ' has-error' : ''));
      $this->labelOptions['class'] = $this->mergeCssClasses($this->labelOptions, 'col-xs-3 control-label');

      if ($this->hasError()) {
        $this->checkboxOptions['aria-describedby'] = $errorId;
      }

      echo CHtml::openTag('div', $this->groupOptions);

      if ($this->label) {
        echo CHtml::tag('label', $this->labelOptions, CHtml::encode($this->label));
      } else {
        echo $this->form->labelEx($this->model, $this->attributeName, $this->labelOptions);
      }

      echo CHtml::openTag('div', ['class' => 'col-xs-9']);
      echo $this->form->checkBox($this->model, $this->attributeName, $this->checkboxOptions);
      echo CHtml::closeTag('div');

      if ($this->description) {
        echo CHtml::openTag('div', ['class' => 'col-xs-9 help-block']);
        echo CHtml::tag('p', [], $this->description);
        echo CHtml::closeTag('div');
      }

      echo $this->form->error($this->model, $this->attributeName, ['class' => 'help-block', 'id' => $errorId]);
      echo CHtml::closeTag('div');

    } else {
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

      if ($this->description) {
        echo CHtml::openTag('div', ['class' => 'help-block']);
        echo CHtml::tag('p', [], $this->description);
        echo CHtml::closeTag('div');
      }

      echo $this->form->error($this->model, $this->attributeName, $this->errorOptions);
      echo CHtml::closeTag('div');

    }
  }
}
