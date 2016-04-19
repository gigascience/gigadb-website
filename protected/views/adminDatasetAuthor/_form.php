<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'dataset-author-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'dataset_id',array('class'=>'control-label')); ?>
				<div class="controls">
        <?= CHtml::activeDropDownList($model,'dataset_id',CHtml::listData(Util::getDois(),'id','identifier')); ?>
		<?php echo $form->error($model,'dataset_id'); ?>
				</div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'author_id',array('class'=>'control-label')); ?>
				<div class="controls">
        <?= CHtml::activeDropDownList($model,'author_id',CHtml::listData(Author::model()->findAll(array('order' => 'surname') ),'id','fullAuthor')) ;?>
		<?php echo $form->error($model,'author_id'); ?>
				</div>
	</div>

        <div class="control-group">
          <?php echo $form->labelEx($model,'rank', array('class'=>'control-label')); ?>
          <div class="controls">
            <?= $form->textField($model,'rank') ?>
            <?php echo $form->error($model,'author_id'); ?>
          </div>
        </div>

	<div class="row buttons">
        <a href="/adminDatasetAuthor/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
