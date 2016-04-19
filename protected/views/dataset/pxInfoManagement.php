<h2>Add proteomics experiment information</h2>
<div class="clear"></div>

<a href="/dataset/datasetManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Study')?></a>
<a href="/dataset/authorManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Author')?></a>
<a href="/dataset/projectManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Project')?></a>
<a href="/dataset/linkManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Link')?></a>
<a href="/dataset/exLinkManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'External Link')?></a>
<a href="/dataset/relatedDoiManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Related Doi')?></a>
<a href="/dataset/sampleManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Sample')?></a>
<a href="#" class="btn sw-selected-btn"><?= Yii::t('app' , 'PX Info')?></a>
<? if($model->files && count($model->files) > 0) { ?>
<a href="/adminFile/create1/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'File')?></a>
<? } ?>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'px-info-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'),
        ));
?>

<div class="span12 form well">
    <div class="form-horizontal">

        <p class="note">Fields with <span class="required">*</span> are required.</p>
        <div class="clear"></div>
        
        <div class="span10">
            <div class="control-group">
                <?php echo $form->labelEx($pxForm, 'keywords', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?php echo $form->textArea($pxForm, 'keywords', array('rows' => 1, 'cols' => 100, 'style' => 'resize:vertical;width:610px')); ?>
                    <?php echo $form->error($pxForm, 'keywords'); ?>
                </div>
            </div>
         </div>

         <div class="span10">
            <div class="control-group">
                <?php echo $form->labelEx($pxForm, 'spp', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?php echo $form->textArea($pxForm, 'spp', array('rows' => 3, 'cols' => 100, 'style' => 'resize:vertical;width:610px')); ?>
                    <?php echo $form->error($pxForm, 'spp'); ?>
                </div>
            </div>
         </div>

         <div class="span10">
            <div class="control-group">
                <?php echo $form->labelEx($pxForm, 'dpp', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?php echo $form->textArea($pxForm, 'dpp', array('rows' => 3, 'cols' => 100, 'style' => 'resize:vertical;width:610px')); ?>
                    <?php echo $form->error($pxForm, 'dpp'); ?>
                </div>
            </div>
         </div>

        <div class="px-column-left">
        	<div class="control-group">
                <div>
                    <?php echo $form->labelEx($pxForm, 'experimentType', array('class' => 'px-label')); ?>
                </div>
                <div class="px-checkbox-list">
                    <?
                    $exTypeList = PxInfoForm::getExTypeList();
                    foreach ( $exTypeList as $exType) {
                    $checkedHtml = array_key_exists($exType, $pxForm->experimentType) ? 'checked="checked"' : '';
                    echo '<input type="checkbox" name="exType[' . $exType . ']" value="1"' . $checkedHtml . '/> '. $exType . '<br/>';
                    }
                    ?>
                    <?php echo $form->textField($pxForm, 'exTypeOther'); ?>
                    <?php echo $form->error($pxForm, 'exTypeOther'); ?>
                </div>
            </div>

           <div class="control-group">
                <div>
                    <?php echo $form->labelEx($pxForm, 'quantification', array('class' => 'px-label')); ?>
                </div>
                <div class="px-checkbox-list">
                    <?
                    $qList = PxInfoForm::getQuantificationList();
                    foreach ( $qList as $quantification) {
                        $checkedHtml = array_key_exists($quantification, $pxForm->quantification) ? 'checked="checked"' : '';
                        echo '<input type="checkbox" name="quantification[' . $quantification . ']" value="1"' . $checkedHtml . '/> '. $quantification . '<br/>';
                    }
                    ?>
                    <?php echo $form->textField($pxForm, 'quantificationOther'); ?>
                    <?php echo $form->error($pxForm, 'quantificationOther'); ?>
                </div>
            </div>
        </div>

        <div class="px-column-center">
        	<div class="control-group">
                <div>
                    <?php echo $form->labelEx($pxForm, 'instrument', array('class' => 'px-label')); ?>
                </div>
                <div class="px-checkbox-list">
                    <?
                    $iList = PxInfoForm::getInstrumentList();
                    foreach ( $iList as $instrument) {
                        $checkedHtml = array_key_exists($instrument, $pxForm->instrument) ? 'checked="checked"' : '';
                        echo '<input type="checkbox" name="instrument[' . $instrument . ']" value="1"' . $checkedHtml . '/> '. $instrument . '<br/>';
                    }
                    ?>
                    <?php echo $form->textField($pxForm, 'instrumentOther'); ?>
                    <?php echo $form->error($pxForm, 'instrumentOther'); ?>
                </div>
            </div>
        </div>

        <div class="px-column-right">
           <div class="control-group">
                <div>
                    <?php echo $form->labelEx($pxForm, 'modification', array('class' => 'px-label')); ?>
                </div>
                <div class="px-checkbox-list">
                    <?
                    $mList = PxInfoForm::getModificationList();
                    foreach ( $mList as $modification) {
                        $checkedHtml = array_key_exists($modification, $pxForm->modification) ? 'checked="checked"' : '';
                        echo '<input type="checkbox" name="modification[' . $modification . ']" value="1"' . $checkedHtml . '/> '. $modification . '<br/>';
                    }
                    ?>
                    <?php echo $form->textField($pxForm, 'modificationOther'); ?>
                    <?php echo $form->error($pxForm, 'modificationOther'); ?>
                </div>
            </div>
        </div>

    </div>

    <div class="span12" style="text-align:center">
        <a href="/dataset/sampleManagement/id/<?= $model->id ?>" class="btn-green">Previous</a>
        <?php echo CHtml::submitButton('Save & Quit', array(
        'class' => 'btn-green delete-title', 
        'name' => 'save-btn',
        'title' => "Save your incomplete submission and leave the submission wizard.",
        )); ?>
        <?php if($model->isIncomplete) { ?>
        <?php echo CHtml::submitButton('Submit', array(
        'class' => 'btn-green delete-title', 
        'name' => 'submit-btn',
        'title' => "Click submit to send information to a curator for review.",
        )); ?>
        <?php } ?>
    </div>

</div>


<?php $this->endWidget(); ?>

<script>
   $(".delete-title").tooltip({'placement':'top'});
</script>