<?php
$this->pageTitle='Forgotten password';
?>
<div class="content">
    <div class="container">
        <section class="page-title-section">
            <div class="page-title">
                <ol class="breadcrumb pull-right">
                    <li><a href="/">Home</a></li>
                    <li class="active">Forgot</li>
                </ol>
                <h4>Forgotten password</h4>
            </div>
        </section>
    <div class="subsection row" style="margin-bottom: 130px;">
        <div class="col-xs-12">
            <?php if(Yii::app()->user->hasFlash('fail-reset-password')): ?>
                <div class="alert alert-warning">
                    <?php echo Yii::app()->user->getFlash('fail-reset-password'); ?>
                </div>
            <?php endif; ?>
        </div>
            <div class="reset-message-div">
                <p>Please enter your email. A link to reset your password will be sent to you.</p>
            </div>
            <div class="create-div">
                <? $form=$this->beginWidget('CActiveForm', array(
                    'id'=>'forgot-password-form',
                    'enableAjaxValidation'=>false,
                    'htmlOptions'=>array('class'=>'form-horizontal')
                )) ?>
                <div class="form-group">
                    <?php echo $form->label($model, 'email', array('class' => 'col-xs-2 control-label')); ?>
                    <div class="col-xs-8">
                        <?php echo $form->textField($model, 'email', array('class' => 'form-control')); ?>
                    </div>
                    <div class="col-xs-2">
                        <?= CHtml::submitButton(Yii::t('app' , 'Reset'), array('class'=>'btn background-btn')) ?>
                    </div>
                </div>
                <? $this->endWidget() ?>
            </div>
        </div>
    </div>
</div>
