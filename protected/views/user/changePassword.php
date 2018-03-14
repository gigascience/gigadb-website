<h2><?=Yii::t('app' , 'Change Password')?></h2>
<div class="clear"></div>
<div class="container">
<div class="row">
                                <div class="col-xs-8 col-xs-offset-2">
                                    <div class="panel panel-default profile-panel">
                                        <div class="panel-body">

		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'ChangePassword-form',
			'enableAjaxValidation'=>false,
			'htmlOptions'=>array('class'=>'form-horizontal'),
		)); ?>

		    <?=isset($error) && $error ? '<div class="row">'.$error.'</div>' : ''?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'password',array('class' => 'col-xs-5 control-label')); ?>
				<div class="col-xs-5">
					<?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>128,'class'=>'form-control', 'value'=>"")); ?>
					<?php echo $form->error($model,'password'); ?>
				</div>
			</div>


		    <div class="form-group">
				<?php echo $form->labelEx($model,'confirmPassword', array('class'=>'col-xs-5 control-label')); ?>
				<div class="col-xs-5">
					<?php echo $form->passwordField($model,'confirmPassword',array('size'=>60,'maxlength'=>128, 'class' => 'form-control')); ?>
					<?php echo $form->error($model,'confirmPassword'); ?>
				</div>
			</div>

			<div class="pull-right">
			<a href="/user/view_profile" class="btn background-btn"><?=Yii::t('app' , 'Cancel')?></a>
				<?php echo CHtml::submitButton(Yii::t('app' , 'Save'),array('class' => 'btn background-btn')); ?>
			</div>
			<div class="clear"></div>
		<?php $this->endWidget(); ?>

		</div><!--span8-->
	</div>
                                    </div>
</div>
</div><!-- user-form -->
<br>
<br>
