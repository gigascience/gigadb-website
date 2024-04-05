<?php

Yii::import('application.components.controls.BaseInput');

class DateField extends BaseInput
{
    public $description = 'Format: mm/dd/yyyy';

    // ** Native date input
    // the native input is accessible, keyboard friendly, screen reader friendly, and overall better than the jQuery datepicker (in my opinion). The only problem is that in Safari + VoiceOver the date input label does not get announced
    //
    // public function run()
    // {
    //     $this->renderControlGroup(function () {
    //         echo $this->form->dateField($this->model, $this->attributeName, $this->inputOptions);
    //     });
    // }
    // ** END OF Native date input

    // ** jQuery ui datepicker
    // I believe the jQuery datepicker was mainly used for backwards compatibility with old browsers, as pointed out here: https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/date#handling_browser_support
    // This datepicker can be interacted with using the keyboard by holding the ctrl key and the arrow keys, but users can't expected to know that. The tooltip is not announced by screen readers so screen reader users must rely on using the input as a text input
    public function run()
    {
        $this->renderControlGroup(function () {
            echo $this->form->textField(
              $this->model,
              $this->attributeName,
              array_merge(
                $this->inputOptions,
                array(
                  'data-js' => 'datepicker',
                  'placeholder' => 'mm/dd/yyyy',
                )
                )
            );
        });
    }

    public function init()
    {
      // NOTE: the script only executes once even with multiple date fields present
      $jsFile = Yii::getPathOfAlias('application.js.datepicker') . '.js';
      $jsUrl = Yii::app()->assetManager->publish($jsFile);
      Yii::app()->clientScript->registerScriptFile($jsUrl, CClientScript::POS_END);

      parent::init();
    }
    // ** END OF jQuery datepicker
}
