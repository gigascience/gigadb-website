<?php
$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>
    <p><h2><?=Yii::t('app' , 'Login')?></h2></p>
<div class="row" id="login">

	<div class="span6">
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
			<div class="control-group">
				<div class="controls">
					<?= $form->checkBox($model,'rememberMe') ?>
					<?= $form->label($model,'rememberMe') ?>
					<?= MyHtml::submitButton(Yii::t('app' ,'Login'), array('class'=>'btn-green pull-right')) ?>
				</div>
			</div>
		</div><!--form-->

		<p class="pull-left"><?= MyHtml::link(Yii::t('app' , "Lost Password"), array('user/reset', 'username'=>$model->username)) ?></p>


		<? $this->endWidget() ?>
	</div>

	<div class="span6">

		<p><?=Yii::t('app' , 'Or login with your preferred identity provider:')?></p>
		<p>&nbsp;</p>
		<div class="form well well-large">
			<div class="row">
				<div class="span4 offset1">
					<div class="content-btnlog">
					     <a class="btn btnlog facebook-log" href="/opauth/facebook">
					         <img src="/images/icons/fb.png"/>&nbsp;&nbsp;<?=Yii::t('app' , 'Facebook')?>
					     </a>
					    <a class="btn btnlog google-log" href="/opauth/google">
					         <img src="/images/icons/google.png"/>&nbsp;&nbsp;<?=Yii::t('app' , 'Google')?>
					    </a>
					 </div>

					 <div class="content-btnlog">
					    <a class="btn btnlog twitter-log" href="/opauth/twitter">
					         <img src="/images/icons/twi.png"/>&nbsp;&nbsp;<?=Yii::t('app' , 'Twitter')?>
					    </a>
					    <a class="btn btnlog linkedin-log" href="/opauth/linkedin">
					        <img src="/images/icons/in.png"/>&nbsp;&nbsp;<?=Yii::t('app' , 'LinkedIn')?>
					    </a>
					 </div>

					  <div class="content-btnlog">
					    <a class="btn btnlog linkedin-log" href="/opauth/orcid">
					        <img src="/images/icons/id.png"/>&nbsp;&nbsp;<?=Yii::t('app' , 'ORCID')?>
					    </a>
					 </div>
					 <input type="hidden"/>
				 </div>
			</div>
		</div>
	</div>
</div><!--login-->