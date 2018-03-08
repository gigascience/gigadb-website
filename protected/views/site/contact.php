
<?
$this->pageTitle='GigaDB - Contact Us';
?>


<? if (Yii::app()->user->hasFlash('contact')) { ?>
<div class="flash-success alert alert-success">
	<?= Yii::app()->user->getFlash('contact'); ?>
</div>
<? } else { ?>
        <div class="content">
            <div class="container">
                <section class="page-title-section">
                    <div class="page-title">
                        <ol class="breadcrumb pull-right">
                            <li><a href="#">Home</a></li>
                            <li class="active">Contact</li>
                        </ol>
                        <h4>Contact</h4>
                    </div>
                </section>
                <div class="subsection">
                    <img src="../images/map.png">
                </div>
                <section>
                    <div class="row">
                        <div class="col-xs-9">
                            <div class="underline-title">
                                <div>
                                    <h4>Contact form</h4>
                                </div>
                            </div>
                            <div class="subsection">
                                <p>For more information or questions regarding submitting data to GigaDB, please contact us at: <a href="mailto:database@gigasciencejournal.com" target="_blank">database@gigasciencejournal.com</a>.</p>
                                <p>Fields with <span class="text-danger">*</span> are required.</p>
                            </div>
                            
                            
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
                <div class="span8 offset2"><?= MyHtml::submitButton('Submit', array('class'=>'btn-green pull-right')); ?></div>

	<? $this->endWidget(); ?>
                        </div>
                        <div class="col-xs-3">
                            <div class="underline-title">
                                <div>
                                    <h4>Contacts</h4>
                                </div>
                            </div>
                            <ul class="fa-ul">
                                <li><i class="fa-li fa fa-home"></i> 58 Street, City the 5008 New Town US</li>
                                <li><i class="fa-li fa fa-envelope"></i> info@gigadb.com</li>
                                <li><i class="fa-li fa fa-phone"></i> 2(333) 35365744</li>
                                <li><i class="fa-li fa fa-globe"></i> http://www.gigadb.org</li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
        </div>


<? } ?>
