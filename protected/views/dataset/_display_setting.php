<?php
    echo CHtml::link('Table Settings', "", // the link for open the dialog
            array(
                'style' => 'cursor: pointer; text-decoration: underline;',
                'onclick' => "{ $('#dialogDisplay').dialog('open');}"
            ));
   
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(// the dialog
        'id' => 'dialogDisplay',
        'options' => array(
            'title' => 'Display Setting',
            'autoOpen' => false,
            'modal' => true,
            'width' => 800,
            'height' => 300,
            'buttons' => array(
                array('text' => 'Submit', 'click' => 'js:function(){ document.myform.submit();}'),
                array('text' => 'Cancel', 'click' => 'js:function(){$(this).dialog("close");}')),
        ),
    ));
    ?>
    <div class="divForForm">
        <form name="myform" method="POST">
        <input type='hidden' name='setting[]' value="name"/> 

            <div class="attribute-setting-item"> 
                <label><strong>Items per page:</strong></label>
                <select name="pageSize" class="selectPageSize">
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
                            <div class="span3"><?= Yii::t('app','File Description') ?></div>
                            <div class="span1"><input type="checkbox" name="setting[]"value="description" 
                                <?= (in_array("description", $setting))? "checked" : ""?>/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="span3"><?= Yii::t('app','Sample ID') ?></div>
                            <div class="span1"><input type="checkbox" name="setting[]"value="sample_id" 
                                <?= (in_array("sample_id", $setting))? "checked" : ""?>/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="span3"><?= Yii::t('app','File Type') ?></div>
                            <div class="span1"><input type="checkbox" name="setting[]" value="type_id"
                            <?= (in_array("type_id", $setting))? "checked" : ""?>/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="span3"><?= Yii::t('app','File Format') ?></div>
                            <div class="span1"><input type="checkbox" name="setting[]" value="format_id"
                            <?= (in_array("format_id", $setting))? "checked" : ""?>/>
                            </div>
                        </div>
                    </div>

                    <div class="span4">
                         <div class="row">
                            <div class="span3"><?= Yii::t('app','Size') ?></div>
                            <div class="span1"><input type="checkbox" name="setting[]" value="size"
                            <?= (in_array("size", $setting))? "checked" : ""?>/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="span3"><?= Yii::t('app','Release Date') ?></div>
                            <div class="span1"><input type="checkbox" name="setting[]" value="date_stamp"
                            <?= (in_array("date_stamp", $setting))? "checked" : ""?>/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="span3"><?= Yii::t('app','Download Link') ?></div>
                            <div class="span1"><input type="checkbox" name="setting[]" value="location"
                            <?= (in_array("location", $setting))? "checked" : ""?>/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="span3"><?= Yii::t('app','File Attributes') ?></div>
                            <div class="span1"><input type="checkbox" name="setting[]" value="attribute"
                            <?= (in_array("attribute", $setting))? "checked" : ""?>/>
                            </div>
                        </div>
                    </div>
                </div>
            <div>
                
        </form>

    </div>    

<?php $this->endWidget(); ?>