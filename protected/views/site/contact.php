<?
$this->pageTitle = 'GigaDB - Contact Us';
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
                <img src="../images/new_interface_image/shekmun_map.png" alt="Map highlighting the GigaDB location on 1 On Kwan Street in Sha Tin, Hong Kong">
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
                            <p>Fields with <span>*</span> are required.</p>
                        </div>



                        <? $form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => 'form contact-form'))); ?>
                        <div class="col-xs-7">
                            <div class="form-group">
                                <?= $form->labelEx($model, 'name', array('class' => 'control-label')); ?>
                                <?= $form->textField($model, 'name', array('class' => 'form-control', 'required' => true, 'aria-required' => 'true', 'aria-describedby' => $model->hasErrors('name') ? 'nameError' : null)); ?>
                                <div id="nameError">
                                    <?php echo $form->error($model, 'name'); ?>
                                </div>

                            </div>
                        </div>

                        <div class="col-xs-7">
                            <div class="form-group">
                                <?= $form->labelEx($model, 'email', array('class' => 'control-label')); ?>

                                <?= $form->textField($model, 'email', array('class' => 'form-control', 'required' => true, 'aria-required' => 'true', 'aria-describedby' => $model->hasErrors('email') ? 'emailError' : null)); ?>
                                <div id="emailError">
                                    <?php echo $form->error($model, 'email'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-7">
                            <div class="form-group">
                                <?= $form->labelEx($model, 'subject', array('class' => 'control-label')); ?>

                                <?= $form->textField($model, 'subject', array('class' => 'form-control', 'required' => true, 'aria-required' => 'true', 'aria-describedby' => $model->hasErrors('subject') ? 'subjectError' : null)); ?>
                                <div id="subjectError">
                                    <?php echo $form->error($model, 'subject'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <?= $form->labelEx($model, 'body', array('class' => 'control-label')); ?>

                                <?= $form->textArea($model, 'body', array('rows' => 5, 'class' => 'form-control', 'required' => true, 'aria-required' => 'true', 'aria-describedby' => $model->hasErrors('body') ? 'bodyError' : null)); ?>
                                <div id="bodyError">
                                    <?php echo $form->error($model, 'body'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-7">
                            <div class="form-group">
                                <?php echo $form->labelEx($model, 'verifyCode'); ?>

                                <div style="width:100%">
                                    <img style="width:200px;" src="<?php echo Yii::app()->captcha->output(); ?>" alt="Type the word in the image">
                                </div>
                                <br>
                                <br>
                                <?php echo $form->textField($model, 'verifyCode', array('class' => 'form-control', 'required' => true, 'aria-required' => 'true', 'aria-describedby' => $model->hasErrors('verifyCode') ? 'verifyCodeHint verifyCodeError' : 'verifyCodeHint')); ?>
                                <div class="hint" id="verifyCodeHint">Please enter the letters as they are shown in the image above.
                                    <br />Letters are case-sensitive.
                                </div>
                                <div id="verifyCodeError">
                                    <?php echo $form->error($model, 'verifyCode'); ?>
                                </div>
                            </div>
                        </div>






                        <div class="span8 offset2"><?= CHtml::submitButton('Submit', array('class' => 'btn background-btn')); ?></div>

                        <? $this->endWidget(); ?>
                    </div><!-- form -->

                    <div class="col-xs-3">
                        <div class="underline-title">
                            <div>
                                <h2 class="h4">Contacts</h2>
                            </div>
                        </div>
                        <address>
                            <ul class="fa-ul">
                                <li><i class="fa-li fa fa-home" aria-hidden="true"></i><span class="sr-only">Address:</span> Room A-D, 26/F, Kings Wing Plaza 2, 1 On Kwan Street, Shek Mun, Shatin, NT, Hong Kong</li>
                                <li><i class="fa-li fa fa-envelope" aria-hidden="true"></i><span class="sr-only">Email:</span> database@gigasciencejournal.com</li>
                                <li><i class="fa-li fa fa-phone" aria-hidden="true"></i><span class="sr-only">Phone:</span> (852) 36103533</li>
                                <li><i class="fa-li fa fa-globe" aria-hidden="true"></i><span class="sr-only">Website:</span> http://www.gigadb.org</li>
                            </ul>
                        </address>
                    </div>
                </div>
            </section>

        </div>
    </div>


<? } ?>