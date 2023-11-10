<?php

Yii::import('application.components.controls.BaseInput');

class DateField extends BaseInput
{
    public function run()
    {
        $this->renderControlGroup(function () {
            echo $this->form->dateField($this->model, $this->attributeName, $this->inputOptions);
        });
    }
}
