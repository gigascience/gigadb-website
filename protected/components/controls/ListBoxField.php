<?php

/**
 * Usage example:
 *
 * $this->widget('application.components.controls.ListboxField', [
 *   'form' => $form,
 *   'model' => $model,
 *   'attributeName' => 'sample_id',
 *   'listDataOptions' => [
 *     'data' => Sample::model()->findAll(),
 *     'valueField' => 'id',
 *     'textField' => 'name',
 *   ],
 *   'inputOptions' => [
 *     'size' => 10,
 *     'multiple' => 'multiple',
 *   ],
 *   'description' => 'Select multiple items by holding Ctrl/Cmd while clicking'
 * ]);
 */

Yii::import('application.components.controls.BaseInput');

class ListboxField extends BaseInput
{
    public $listDataOptions = [];
    public $dataset = [];

    public function init()
    {
        // NOTE: the script only executes once even with multiple date fields present
        $jsFile = Yii::getPathOfAlias('application.js.listBox') . '.js';
        $jsUrl = Yii::app()->assetManager->publish($jsFile);
        Yii::app()->clientScript->registerScriptFile($jsUrl, CClientScript::POS_END);

        parent::init();

        if (!isset($this->inputOptions['class'])) {
            $this->inputOptions['class'] = 'form-control mb-10';
        }

        if (!isset($this->inputOptions['multiple'])) {
            $this->inputOptions['multiple'] = 'multiple';
        }

        if (!isset($this->inputOptions['size'])) {
            $this->inputOptions['size'] = 10;
        }

        if (!isset($this->inputOptions['data-js'])) {
            $this->inputOptions['data-js'] = 'listbox';
        }
    }

    public function run()
    {
        $this->renderControlGroup(function () {
            if ($this->dataset) {
                $dataset = $this->dataset;
            } else {
                $data = $this->listDataOptions['data'] ?? [];
                $valueField = $this->listDataOptions['valueField'] ?? 'id';
                $textField = $this->listDataOptions['textField'] ?? 'name';

                $dataset = CHtml::listData($data, $valueField, $textField);
            }

            // Sort the dataset alphabetically by values (display text)
            asort($dataset, SORT_STRING | SORT_FLAG_CASE);

            echo CHtml::activeListBox(
                $this->model,
                $this->attributeName,
                $dataset,
                $this->inputOptions
            );
        });
    }
}
