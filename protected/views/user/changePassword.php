<h2><?=Yii::t('app' , 'Change Password')?></h2>
<div class="clear"></div>
<div class="row">
	<div class="span8 offset2">
		<div class="form well">

		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'ChangePassword-form',
			'enableAjaxValidation'=>false,
			'htmlOptions'=>array('class'=>'form-horizontal'),
		)); ?>

		    <?=isset($error) && $error ? '<div class="row">'.$error.'</div>' : ''?>

			<div class="control-group">
				<?php echo $form->labelEx($model,'password',array('class' => 'required control-label')); ?>
				<div class="controls">
					<?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>128,'class'=>'input_field', 'value'=>"")); ?>
					<?php echo $form->error($model,'password'); ?>
				</div>
			</div>


		    <div class="control-group">
				<?php echo $form->labelEx($model,'confirmPassword', array('class'=>'required control-label')); ?>
				<div class="controls">
					<?php echo $form->passwordField($model,'confirmPassword',array('size'=>60,'maxlength'=>128, 'class' => 'input_field')); ?>
					<?php echo $form->error($model,'confirmPassword'); ?>
				</div>
			</div>

			<div class="pull-right">
			<a href="/user/view_profile" class="btn"><?=Yii::t('app' , 'Cancel')?></a>
				<?php echo CHtml::submitButton(Yii::t('app' , 'Save'),array('class' => 'btn-green')); ?>
			</div>
			<div class="clear"></div>
		<?php $this->endWidget(); ?>

		</div><!--span8-->
	</div>
</div><!-- user-form -->
