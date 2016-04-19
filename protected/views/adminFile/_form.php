<?php
$cs = Yii::app()->getClientScript();
$cssCoreUrl = $cs->getCoreScriptUrl();
$cs->registerCssFile($cssCoreUrl . '/jui/css/base/jquery-ui.css');
Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-1.8.21.custom.min.js');
Yii::app()->clientScript->registerScriptFile('/js/jquery-migrate-1.2.1.js', CClientScript::POS_END);
?>

<div class="row">
	<div class="span10 offset1 form well">
		
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'file-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>array('class'=>'form-horizontal')
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
			<?php echo $form->labelEx($model,'name',array('class'=>'control-label')); ?>
			<div class="controls">
				<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>100)); ?>
				<?php echo $form->error($model,'name'); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model,'location',array('class'=>'control-label')); ?>
			<div class="controls">
				<?php echo $form->textField($model,'location',array('size'=>60,'maxlength'=>200)); ?>
				<?php echo $form->error($model,'location'); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model,'extension',array('class'=>'control-label')); ?>
			<div class="controls">
				<?php echo $form->textField($model,'extension',array('size'=>30,'maxlength'=>30)); ?>
				<?php echo $form->error($model,'extension'); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model,'size',array('class'=>'control-label')); ?>
			<div class="controls">
				<?php echo $form->textField($model,'size'); ?>
				<?php echo $form->error($model,'size'); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model,'description',array('class'=>'control-label')); ?>
			<div class="controls">
				<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
				<?php echo $form->error($model,'description'); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model,'date_stamp',array('class'=>'control-label')); ?>
			<div class="controls">
				<?php echo $form->textField($model,'date_stamp' , array('class' => 'date')); ?>
				<?php echo $form->error($model,'date_stamp'); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model,'format_id',array('class'=>'control-label')); ?>
			<div class="controls">
	        	<?= CHtml::activeDropDownList($model,'format_id',CHtml::listData(FileFormat::model()->findAll(),'id','name')); ?>
				<?php echo $form->error($model,'format_id'); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model,'type_id',array('class'=>'control-label')); ?>
			<div class="controls">
	        	<?= CHtml::activeDropDownList($model,'type_id',CHtml::listData(FileType::model()->findAll(),'id','name')); ?>
				<?php echo $form->error($model,'type_id'); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model,'sample_name',array('class'=>'control-label')); ?>
			<div class="controls">
	        			<?= CHtml::activeDropDownList($model,'sample_name',CHtml::listData(Sample::model()->findAll(),'id','name'),array('empty' => 'None',)); ?>
				<?php echo $form->error($model,'sample_name'); ?>
			</div>
		</div>

		<?php if(!$model->isNewRecord) { ?>
		<div class="control-group">
			<a href="#" role="button" class="btn btn-attr">New Attribute </a>
			<br/>
			<div class="js-new-attr" style="display:none;">				
				<?php echo CHtml::activeDropDownList($attribute, 'attribute_id',CHtml::listData(Attribute::model()->findAll(),'id','attribute_name'), array('class'=>'attr-form', 'empty'=>'Select name')); ?>
				<?php echo $form->textField($attribute, 'value',array('class'=>'attr-form'));?> 
				<?php echo CHtml::activeDropDownList($attribute, 'unit_id',CHtml::listData(Unit::model()->findAll(),'id','name'), array('class'=>'attr-form', 'empty'=>'Select unit')); ?>
				<input type="submit" class="btn" name="submit_attr" value="Add"/>
			</div>
			<br/>
			<?php if($model->fileAttributes) { ?>
			<table class="table">
				<thead>
					<tr>
						<th>Attribute Name</th>
						<th>Value</th>
						<th>Unit</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($model->fileAttributes as $fa) { ?>
					<tr class="row-edit-<?= $fa->id ?>">
						<td><?= $fa->attribute->attribute_name ?></td>
						<td><?= $fa->value ?></td>
						<td><?= $fa->unit ? $fa->unit->name : '' ?></td>
						<td><a role="button" class="btn btn-edit js-edit" data="<?= $fa->id ?>">Edit</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php } ?>
			
		</div>
		<?php } ?>
		<div class="pull-right">
	        <a href="/adminFile/admin" class="btn">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
		</div>

	<?php $this->endWidget(); ?>



	</div><!-- form -->
</div>
<script type="text/javascript">
$('.date').datepicker({'dateFormat': 'yy-mm-dd'});
$('.btn-attr').click(function(e) {
	e.preventDefault();
	$('.js-new-attr').toggle();
})
$('.js-edit').click(function(e) {
	e.preventDefault();
	id = $(this).attr('data');
	
	row = $('.row-edit-'+id);
	if(id) {
		$.post('/adminFile/editAttr', {'id': id}, function(result) {
			if(result.success) {
				row.html(result.data);
				//$('.js-new-attr').remove();
			}
		}, 'json');
	}
})
</script>
