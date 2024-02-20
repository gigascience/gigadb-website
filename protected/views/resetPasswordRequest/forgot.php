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
        <div class="col-xs-6 col-xs-offset-3 well">
          <div class="reset-message-div">
            <p class="mb-20">Please enter your email. A link to reset your password will be sent to you.</p>
          </div>
                <? $form = $this->beginWidget(
                  'CActiveForm',
                  array(
                    'id' => 'forgot-password-form',
                    'enableAjaxValidation' => false,
                    'htmlOptions' => array('class' => 'form-horizontal forgot-password-form')
                  )
                ) ?>
                <div class="form-group">
                    <?php echo $form->label($model, 'email', array('class' => 'col-xs-2 control-label')); ?>
                    <div class="col-xs-10">
                        <?php echo $form->emailField($model, 'email', array('class' => 'form-control', 'required' => 'true')); ?>
                    </div>
                </div>
                <div class="pull-right">
                    <?= CHtml::submitButton(Yii::t('app', 'Reset Password'), array('class' => 'btn background-btn forgot-password-btn')) ?>
                </div>
                <? $this->endWidget() ?>
            </div>
        </div>
    </div>
</div>
