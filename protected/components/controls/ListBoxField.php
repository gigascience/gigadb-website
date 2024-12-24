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
    public array $listDataOptions = [];
    public array $dataset = [];

    private const DEFAULT_CLASS = 'form-control mb-10';
    private const DEFAULT_SIZE = 10;
    private const DEFAULT_DESCRIPTION = 'Select multiple items by holding Ctrl/Cmd while clicking';

    public function init(): void
    {
        parent::init();

        $this->inputOptions['class'] ??= self::DEFAULT_CLASS;
        $this->inputOptions['multiple'] ??= 'multiple';
        $this->inputOptions['size'] ??= self::DEFAULT_SIZE;
        $this->inputOptions['data-js'] ??= 'listbox';
        $this->description ??= self::DEFAULT_DESCRIPTION;
    }

    public function run(): void
    {
        $this->renderControlGroup(function (): void {
            $dataset = $this->getDataset();

            // Sort the dataset alphabetically by option label
            asort($dataset, SORT_STRING | SORT_FLAG_CASE);

            echo CHtml::activeListBox(
                $this->model,
                $this->attributeName,
                $dataset,
                $this->inputOptions
            );
        });
    }

    private function getDataset(): array
    {
        if (!empty($this->dataset)) {
            return $this->dataset;
        }

        $data = $this->listDataOptions['data'] ?? [];
        $valueField = $this->listDataOptions['valueField'] ?? 'id';
        $textField = $this->listDataOptions['textField'] ?? 'name';

        return CHtml::listData($data, $valueField, $textField);
    }
}
