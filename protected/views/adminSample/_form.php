<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'sample-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'species_id',array('class'=>'control-label')); ?>
				<div class="controls">
          <?php
                    $criteria = new CDbCriteria;
                    $criteria->select = 't.id, t.common_name'; // select fields which you want in output
                    $criteria->limit = 100;
//                    $criteria->condition = 't.status = 1';
                    ?>

                    <?php
                    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                        'name' => 'name',
                        'model' => $model,
                        'attribute' => 'species_id',
                        'source' => $this->createUrl('/adminDatasetSample/autocomplete'),
                        'options' => array(
                            'minLength' => '2',
                        ),
                        'htmlOptions' => array(
                            'placeholder' => 'name',
                            'size' => 'auto',
                            'value' => $specie->tax_id .':'. $specie->scientific_name,
                        ),
                    ));
                    ?>
                    <?php
//                    CHtml::activeDropDownList($model, 'species_id', CHtml::listData(Species::model()->findAll($criteria), 'id', 'common_name'));
                    ?>


                    <?php echo $form->error($model, 'species_id'); ?>
                </div>
	</div>
        
        <div class="control-group">
            <?php echo $form->labelEx($model,'attributesList',array('class'=>'control-label')); ?>
            <div class="controls">
                <textarea name="Sample[attributesList]"><?= $model->getAttributesList(true) ?></textarea>
                <?php echo $form->error($model,'attributesList'); ?>
            </div>
        </div>
        
        <div class="control-group">
            <?php echo $form->labelEx($model,'name',array('class'=>'control-label')); ?>
            <div class="controls">
                <?php echo $form->textField($model,'name',array('size' => 50,'maxlength' => 50)) ?>
                <?php echo $form->error($model,'name'); ?>
            </div>
        </div>

	<div class="pull-right">
        <a href="/adminSample/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
    </div>
</div>
