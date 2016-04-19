<?php
    echo CHtml::link('Table Settings', "", // the link for open the dialog
            array(
                'style' => 'cursor: pointer; text-decoration: underline;',
                'onclick' => "{ $('#sampleDialog').dialog('open');}"
            ));
   
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(// the dialog
        'id' => 'sampleDialog',
        'options' => array(
            'title' => 'Display Setting',
            'autoOpen' => false,
            'modal' => true,
            'width' => 800,
            'height' => 320,
            'buttons' => array(
                array('text' => 'Submit', 'click' => 'js:function(){ document.sampleSetting.submit();}'),
                array('text' => 'Cancel', 'click' => 'js:function(){$(this).dialog("close");}')),
        ),
    ));
    ?>
    <div class="divForForm">
        <form name="sampleSetting" method="POST"> 
            <input type='hidden' name='columns[]' value="name"/>
            
            <div class="attribute-setting-item"> 
                <label><strong>Items per page:</strong></label>
                <select name="perPage" class="selectPageSize">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>                 
                </select>
            </div>

            <div class="attribute-setting-content">

                <div class="row">
                    <div ><h3>Columns:</h3></div>
                    <div class="span4">
                        <div class="row">
                            <div class="span3"><?= Yii::t('app','Common Name') ?></div>
                            <div class="span1">
                                <input type="checkbox" name="columns[]" value="common_name"
                                <?= (in_array("common_name", $columns))? "checked" : ""?>/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="span3"><?= Yii::t('app','Scienfic Name') ?></div>
                            <div class="span1">
                                <input type="checkbox" name="columns[]" value="scientific_name"
                                <?= (in_array("scientific_name", $columns))? "checked" : ""?>/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="span3"><?= Yii::t('app','Sample Attributes') ?></div>
                            <div class="span1">
                                <input type="checkbox" name="columns[]" value="attribute"
                                <?= (in_array("attribute", $columns))? "checked" : ""?>/>
                            </div>
                        </div>
                    </div>

                    <div class="span4">
                         <div class="row">
                            <div class="span3"><?= Yii::t('app','Taxonomic ID') ?></div>
                            <div class="span1">
                                <input type="checkbox" name="columns[]" value="taxonomic_id"
                                <?= (in_array("taxonomic_id", $columns))? "checked" : ""?>/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="span3"><?= Yii::t('app','Genbank Name') ?></div>
                            <div class="span1">
                                <input type="checkbox" name="columns[]" value="genbank_name"
                                <?= (in_array("genbank_name", $columns))? "checked" : ""?>/>
                            </div>
                        </div>
                    </div>
                </div>
            <div>

        </form>
    </div>    

<?php $this->endWidget(); ?>