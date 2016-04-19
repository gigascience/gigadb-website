
<?
$this->pageTitle='GigaDB - Contact Us';
?>


<? if (Yii::app()->user->hasFlash('contact')) { ?>
<div class="flash-success alert alert-success">
	<?= Yii::app()->user->getFlash('contact'); ?>
</div>
<? } else { ?>
<h2>Contact</h2>
<div class="row" id="contact">
	<div class="span8 offset2">
		<p>For more information or questions regarding submitting data to <em>Giga</em>DB, please contact us at: <a href="mailto:database@gigasciencejournal.com" target="_blank">database@gigasciencejournal.com</a></p>
		<p>Fields with <span class="required">*</span> are required.</p>

		<div class="form well">
			<? $form=$this->beginWidget('CActiveForm', array('htmlOptions'=>array('class'=>'form-horizontal'))); ?>
				<div class="control-group">
					<?= $form->labelEx($model,'name', array('class'=>'control-label')); ?>
					<div class="controls">
						<?= $form->textField($model,'name'); ?>
						<?php echo $form->error($model,'name'); ?>
					</div>
				</div>

				<div class="control-group">
					<?= $form->labelEx($model,'email', array('class'=>'control-label')); ?>
					<div class="controls">
						<?= $form->textField($model,'email'); ?>
						<?php echo $form->error($model,'email'); ?>
					</div>
				</div>

				<div class="control-group">
					<?= $form->labelEx($model,'subject', array('class'=>'control-label')); ?>
					<div class="controls">
						<?= $form->textField($model,'subject',array('size'=>60,'maxlength'=>128)); ?>
						<?php echo $form->error($model,'subject'); ?>
					</div>
				</div>

				<div class="control-group">
					<?= $form->labelEx($model,'body', array('class'=>'control-label')); ?>
					<div class="controls">
						<?= $form->textArea($model,'body',array('rows'=>6)); ?>
						<?php echo $form->error($model,'body'); ?>
					</div>
				</div>

				<div class="control-group">
			        <div class="controls">
			        	<?= $form->labelEx($model, 'validacion') ?>
			        	<? $this->widget('application.extensions.recaptcha.EReCaptcha',
			            array('model'=>$model, 'attribute'=>'validacion',
			                  'theme'=>'clean', 'language'=>'zh_TW',
			                  'publicKey'=>Yii::app()->params['recaptcha_publickey'])) ?>
			        </div>
				</div>

				

			
		</div><!-- form -->

	</div>
	<div class="span8 offset2"><?= MyHtml::submitButton('Submit', array('class'=>'btn-green pull-right')); ?></div>

	<? $this->endWidget(); ?>
</div>

<? } ?>
