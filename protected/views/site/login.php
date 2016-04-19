<?php
$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>
    <h2><?=Yii::t('app' , 'Login')?></h2>
<div class="row" id="login">
	<div class="span6 offset3">
	<p><?=Yii::t('app' , 'Please fill out the following form with your login credentials:')?></p>
	<p><?=Yii::t('app' , 'Fields with <span class="required">*</span> are required.')?></p>
		<div class="form well">
			<? $form = $this->beginWidget('CActiveForm', array('htmlOptions'=>array('class'=>'form-horizontal'))) ?>
			<div class="control-group">
				<?= $form->labelEx($model,'username', array('class'=>'control-label')) ?>
				<div class="controls">
					<?= $form->textField($model,'username') ?>
					<?php echo $form->error($model,'username'); ?>
				</div>

			</div>

			<div class="control-group">
				<?= $form->labelEx($model,'password', array('class'=>'control-label')) ?>
				<div class="controls">
					<?= $form->passwordField($model,'password') ?>
					<?php echo $form->error($model,'password'); ?>
				</div>
			</div>
			<div class="controls">
				<?= $form->checkBox($model,'rememberMe') ?>
				<?= $form->label($model,'rememberMe') ?>
			</div>
		</div><!--form-->

		<p class="pull-left"><?= MyHtml::link(Yii::t('app' , "Lost Password"), array('user/reset', 'username'=>$model->username)) ?></p>
		<?= MyHtml::submitButton(Yii::t('app' ,'Login'), array('class'=>'btn-green pull-right')) ?>

		<? $this->endWidget() ?>
	</div>
</div><!--login-->
