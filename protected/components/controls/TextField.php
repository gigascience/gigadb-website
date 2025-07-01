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
 *    'inputWrapperOptions' => 'my-wrapper-class' // optional, used mainly in conjunction with form-horizontal
 *    'lengthWarningOptions' => [
 *      'threshold' => 100,
 *      'showCount' => true, // optional
 *      'message'   => 'Warning: Input text is over 100 characters long, you should reduce it if possible.', // optional
 *    ], // optional
 *  ]);
 */

Yii::import('application.components.controls.BaseInput');

class TextField extends BaseInput
{

  public $lengthWarningOptions;

  public function run()
  {
    if ($this->hasLengthWarning()) {
      if (!is_array($this->inputOptions)) {
        $this->inputOptions = [];
      }

      $this->inputOptions = array_merge(
        $this->inputOptions,
        [
          'data-length-threshold' => $this->lengthWarningOptions['threshold'],
          'data-length-show-count' => $this->lengthWarningOptions['showCount'] ? 'true' : 'false',
        ]
      );
    }

    $this->renderControlGroup(function () {
       echo $this->form->textField($this->model, $this->attributeName, $this->inputOptions);

      if ($this->hasLengthWarning()) {
        $warningMessage = $this->lengthWarningOptions['message'] ?: "Warning: Input text is over {$this->lengthWarningOptions['threshold']} characters long, you should reduce it if possible.";
        $inputId = CHtml::activeId($this->model, $this->attributeName);

        Yii::app()->controller->renderPartial(
           '//shared/_lengthWarning',
           [
               'showCount'      => $this->showCount(),
               'threshold'      => $this->lengthWarningOptions['threshold'],
               'warningMessage' => $warningMessage,
               'inputId'        => $inputId,
               'initialCount'   => mb_strlen($this->model->{$this->attributeName}),
           ]
       );
      }
    });
  }

  /**
   * Determines if the length warning feature is enabled and valid.
   *
   * @return bool
   */
  protected function hasLengthWarning()
  {
    return isset($this->lengthWarningOptions)
      && !empty($this->lengthWarningOptions)
      && array_key_exists('threshold', $this->lengthWarningOptions)
      && $this->lengthWarningOptions['threshold'] !== null
      && $this->lengthWarningOptions['threshold'] >= 0;
  }

  protected function showCount()
  {
    return isset($this->lengthWarningOptions)
      && !empty($this->lengthWarningOptions)
      && array_key_exists('showCount', $this->lengthWarningOptions)
      && $this->lengthWarningOptions['showCount'] !== null
      && $this->lengthWarningOptions['showCount'] === true;
  }

  /**
   * Publishes and registers the length-warning JavaScript once per request.
   */
  protected function registerLengthWarningScript()
  {
    Yii::app()->assetManager->forceCopy = YII_DEBUG; // Ensure fresh copy during development

    $jsDir = Yii::getAlias('/gigadb/app/client/js');
    $jsUrl = Yii::app()->assetManager->publish($jsDir);
    $jsPath = $jsUrl . '/length-warning.js';

    Yii::app()->clientScript->registerScriptFile($jsPath, CClientScript::POS_END, ['type' => 'module']);
  }

  public function init()
  {
    if ($this->hasLengthWarning()) {
      $this->registerLengthWarningScript();
    }

    parent::init();
  }
}
