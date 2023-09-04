
<?
	$this->pageTitle='GigaDB - Contact Us';
?>


<? if (Yii::app()->user->hasFlash('contact')) { ?>
<div class="flash-success alert alert-success">
	<?= Yii::app()->user->getFlash('contact'); ?>
</div>
<? } else {
    Yii::app()->captcha->generate();
?>
 <div class="content">
            <div class="container">
                <section class="page-title-section">
                    <div class="page-title">
                        <ol class="breadcrumb pull-right">
                            <li><a href="/">Home</a></li>
                            <li class="active">Contact</li>
                        </ol>
                        <h1 class="h4">Contact</h1>
                    </div>
                </section>
                <div class="subsection">
                    <img src="../images/new_interface_image/shekmun_map.png">
                </div>
                <section>
                    <div class="row">
                        <div class="col-xs-9">
                            <div class="underline-title">
                                <div>
                                    <h2 class="h4">Contact form</h2>
                                </div>
                            </div>
                            <div class="subsection">
                                <p>For more information or questions regarding submitting data to GigaDB, please contact us at: <a href="mailto:database@gigasciencejournal.com" target="_blank">database@gigasciencejournal.com</a>.</p>
                                <p>Fields with <span class="text-danger">*</span> are required.</p>
                            </div>



			<? $form=$this->beginWidget('CActiveForm', array('htmlOptions'=>array('class'=>'form contact-form'))); ?>
				<div class="col-xs-7">
                                    <div class="form-group">
					<?= $form->labelEx($model,'name', array('class'=>'text-danger')); ?>
                                        <?= $form->textField($model,'name',array('class'=>'form-control')); ?>
                                        <?php echo $form->error($model,'name'); ?>

                                     </div>
                                 </div>

				<div class="col-xs-7">
                                    <div class="form-group">
					<?= $form->labelEx($model,'email', array('class'=>'control-label')); ?>

						<?= $form->textField($model,'email',array('class'=>'form-control')); ?>
						<?php echo $form->error($model,'email'); ?>
					</div>
				</div>

				<div class="col-xs-7">
                                    <div class="form-group">
					<?= $form->labelEx($model,'subject', array('class'=>'control-label')); ?>

						<?= $form->textField($model,'subject',array('class'=>'form-control')); ?>
						<?php echo $form->error($model,'subject'); ?>
					</div>
				</div>

				<div class="col-xs-12">
                                    <div class="form-group">
					<?= $form->labelEx($model,'body', array('class'=>'control-label')); ?>

						<?= $form->textArea($model,'body',array('rows'=>5,'class'=>'form-control')); ?>
						<?php echo $form->error($model,'body'); ?>
					</div>
				</div>

                                <div class="col-xs-7">
                                    <div class="form-group">
					<?php echo $form->labelEx($model,'verifyCode'); ?>

						<div style="width:100%">
							<img style="width:200px;" src="<?php echo Yii::app()->captcha->output(); ?>">
						</div>
                                                <br>
                                                <br>
						<?php echo $form->textField($model,'verifyCode',array('class'=>'form-control')); ?>
						<div class="hint">Please enter the letters as they are shown in the image above.
						<br/>Letters are case-sensitive.</div>
						<?php echo $form->error($model, 'verifyCode'); ?>
						</div>
                                </div>






                <div class="span8 offset2"><?= CHtml::submitButton('Submit', array('class'=>'btn background-btn')); ?></div>

                <? $this->endWidget(); ?>
                </div><!-- form -->

                        <div class="col-xs-3">
                            <div class="underline-title">
                                <div>
                                    <h2 class="h4">Contacts</h2>
                                </div>
                            </div>
                            <ul class="fa-ul">
                                <li><i class="fa-li fa fa-home"></i> Room A-D, 26/F, Kings Wing Plaza 2, 1 On Kwan Street, Shek Mun, Shatin, NT, Hong Kong</li>
                                <li><i class="fa-li fa fa-envelope"></i> database@gigasciencejournal.com</li>
                                <li><i class="fa-li fa fa-phone"></i> (852) 36103533</li>
                                <li><i class="fa-li fa fa-globe"></i> http://www.gigadb.org</li>
                            </ul>
                        </div>
                </div>
                </section>

            </div>
        </div>


<? } ?>
