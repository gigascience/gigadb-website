<?php

Yii::import('zii.widgets.jui.CJuiAutoComplete');

class DeferrableCJuiAutoComplete extends CJuiAutoComplete
{
    /**
     * Run this widget.
     * This method registers necessary javascript and renders the needed HTML code.
     */
    public function run()
    {
        list($name,$id) = $this->resolveNameID();

        if (isset($this->htmlOptions['id'])) {
            $id = $this->htmlOptions['id'];
        } else {
            $this->htmlOptions['id'] = $id;
        }
        if (isset($this->htmlOptions['name'])) {
            $name = $this->htmlOptions['name'];
        }

        if ($this->hasModel()) {
            echo CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
        } else {
            echo CHtml::textField($name, $this->value, $this->htmlOptions);
        }

        if ($this->sourceUrl !== null) {
            $this->options['source'] = CHtml::normalizeUrl($this->sourceUrl);
        } else {
            $this->options['source'] = $this->source;
        }

        $options = CJavaScript::encode($this->options);
        $script = "document.addEventListener('DOMContentLoaded', function(event) {
				jQuery('#{$id}').autocomplete($options);
			});
		";
        // Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$id, $script);
        echo CHtml::script($script);
    }
}
