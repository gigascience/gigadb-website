<?php

class BaseInput extends CWidget
{
  public $form;
  public $model;
  public $attributeName;
  public $description;
  public $inputOptions;
  public $labelOptions;
  public $errorOptions;
  public $groupOptions;

  protected function hasError() {
    return $this->model->hasErrors($this->attributeName);
  }

  private function mergeCssClasses($options, $defaultClass)
  {
    return isset($options['class']) ? "{$defaultClass} {$options['class']}" : $defaultClass;
  }

  protected function prepareHtmlOptions()
  {
    $this->groupOptions['class'] = $this->mergeCssClasses($this->groupOptions, 'form-group' . ($this->hasError() ? ' has-error' : ''));
    $this->inputOptions['class'] = $this->mergeCssClasses($this->inputOptions, 'form-control');
    $this->labelOptions['class'] = $this->mergeCssClasses($this->labelOptions, 'control-label');
    $this->errorOptions['class'] = $this->mergeCssClasses($this->errorOptions, 'control-error help-block');

    if ($this->description) {
      $describedBy[] = $this->attributeName . '-desc';
    }

    if ($this->model->hasErrors($this->attributeName)) {
      $describedBy[] = $this->attributeName . '-error';
    }

    if (!empty($describedBy)) {
      $this->inputOptions['aria-describedby'] = implode(" ", $describedBy);
    }

    if (isset($this->inputOptions['required']) && $this->inputOptions['required']) {
      $this->inputOptions['aria-required'] = 'true';
    }
  }

  protected function renderLabel()
  {
    echo $this->form->labelEx($this->model, $this->attributeName, $this->labelOptions);
  }

  protected function renderError()
  {
    echo CHtml::openTag('div', array_merge(['id' => $this->attributeName . '-error'], $this->errorOptions));
    if ($this->hasError()) {
      echo $this->form->error($this->model, $this->attributeName);
    }
    echo CHtml::closeTag('div');
  }

  protected function renderDescription()
  {
    if ($this->description) {
      echo CHtml::tag('p', array('id' => $this->attributeName . '-desc', 'class' => 'control-description help-block'), $this->description);
    }
  }

  public function renderControlGroup($inputClosure)
  {
    $this->prepareHtmlOptions();

    echo CHtml::openTag('div', $this->groupOptions);
    $this->renderLabel();
    $inputClosure();
    $this->renderDescription();
    $this->renderError();
    echo CHtml::closeTag('div');
  }
}
