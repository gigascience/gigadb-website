<?php
class CheckBoxField extends CWidget
{
  public $form;
  public $model;
  public $attributeName;
  public $groupOptions;
  public $checkboxOptions;
  public $labelOptions;
  public $errorOptions;

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
    $this->groupOptions['class'] = $this->mergeCssClasses($this->groupOptions, 'form-group checkbox checkbox-green' . ($this->hasError() ? ' has-error' : ''));
    $this->labelOptions['class'] = $this->mergeCssClasses($this->labelOptions, 'control-label');
    $this->errorOptions['class'] = $this->mergeCssClasses($this->errorOptions, 'help-block');
    $this->errorOptions['id'] = $errorId;

    if ($this->hasError()) {
      $this->checkboxOptions['aria-describedby'] = $errorId;
    }

    echo CHtml::openTag('div', $this->groupOptions);
    echo $this->form->checkBox($this->model, $this->attributeName, $this->checkboxOptions);
    echo $this->form->labelEx($this->model, $this->attributeName, $this->labelOptions);
    echo $this->form->error($this->model, $this->attributeName, $this->errorOptions);
    echo CHtml::closeTag('div');
  }
}
