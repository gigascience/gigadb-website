<?php
$this->pageTitle = 'Forgotten password';
?>
<div class="content">
    <div class="container">
      <?php
      $this->widget('TitleBreadcrumb', [
        'pageTitle' => 'Forgotten password',
        'breadcrumbItems' => [
          ['label' => 'Home', 'href' => '/'],
          ['isActive' => true, 'label' => 'Forgot'],
        ]
      ]);
      ?>
    <div class="subsection row">
        <div class="col-xs-12">
            <?php if (Yii::app()->user->hasFlash('fail-reset-password')): ?>
                <div class="alert alert-warning">
                    <?php echo Yii::app()->user->getFlash('fail-reset-password'); ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
            <div class="well">
                <div class="mb-10">
                    <p>Please enter your email. A link to reset your password will be sent to you.</p>
                </div>
                <?php $form = $this->beginWidget(
                    'CActiveForm',
                    array(
                        'id' => 'forgot-password-form',
                        'enableAjaxValidation' => false,
                        'htmlOptions' => array('class' => 'form-horizontal forgot-password-form')
                    )
                ) ?>
                <div class="form-group">
                    <?php echo $form->label($model, 'email', array('class' => 'col-sm-3 col-xs-12 control-label')); ?>
                    <div class="col-sm-9 col-xs-12">
                        <?php echo $form->emailField($model, 'email', array('class' => 'form-control', 'required' => 'true')); ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <?= CHtml::submitButton(Yii::t('app', 'Reset Password'), array('class' => 'btn background-btn forgot-password-btn pull-right')) ?>
                    </div>
                </div>
                <?php $this->endWidget() ?>
            </div>
        </div>
    </div>
</div>
