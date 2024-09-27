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
  public $inputWrapperOptions;
  // NOTE: bootstrap tooltips override the aria-describedby attribute, losing all values it previously held. So if using a tooltip, it is assumed that a description is not needed
  public $tooltip = '';

  protected function hasError()
  {
    return $this->model->hasErrors($this->attributeName);
  }

  protected function registerTooltipScript() {
    $jsFile = Yii::getPathOfAlias('application.js.bootstrap-tooltip-init') . '.js';
    $jsUrl = Yii::app()->assetManager->publish($jsFile);
    Yii::app()->clientScript->registerScriptFile($jsUrl, CClientScript::POS_END);
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

    if ($this->description && !$this->tooltip) {
      $describedBy[] = $this->attributeName . '-desc';
    }

    if ($this->model->hasErrors($this->attributeName)) {
      $describedBy[] = $this->attributeName . '-error';
    }

    if (isset($this->inputOptions['required']) && $this->inputOptions['required']) {
      $this->inputOptions['aria-required'] = 'true';
    }

    if (!empty($this->tooltip)) {
      $this->inputOptions['title'] = $this->tooltip;
      $this->inputOptions['data-toggle'] = 'tooltip';
    }

    if (!empty($describedBy)) {
      $this->inputOptions['aria-describedby'] = implode(" ", $describedBy);
    }
  }

  protected function renderLabel()
  {
    CHtml::$afterRequiredLabel = '<span aria-hidden="true"> *</span>';
    echo $this->form->labelEx($this->model, $this->attributeName, $this->labelOptions);
  }

  protected function renderError()
  {
    echo CHtml::openTag('div', array_merge(['id' => $this->attributeName . '-error', 'role' => 'alert'], $this->errorOptions));
    if ($this->hasError()) {
      echo $this->form->error($this->model, $this->attributeName);
    }
    echo CHtml::closeTag('div');
  }

  protected function renderDescription()
  {
    if ($this->description && !$this->tooltip) {
      echo CHtml::tag('p', array('id' => $this->attributeName . '-desc', 'class' => 'control-description help-block'), $this->description);
    }
  }

  public function renderControlGroup($inputClosure)
  {
    $this->prepareHtmlOptions();

    echo CHtml::openTag('div', $this->groupOptions);
    $this->renderLabel();
    echo CHtml::openTag('div', array('class' => $this->inputWrapperOptions));
    $inputClosure();
    $this->renderDescription();
    $this->renderError();
    echo CHtml::closeTag('div');
    echo CHtml::closeTag('div');
  }

  public function init()
  {
    if (!empty($this->tooltip)) {
      $this->registerTooltipScript();
    }

    parent::init();
  }
}
