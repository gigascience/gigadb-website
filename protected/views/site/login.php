<?php
$this->pageTitle = Yii::app()->name . ' - Login';
$this->breadcrumbs = array(
    'Login',
);
?>

<section>
    <div class="container" id="login">
      <?php
        $this->widget('TitleBreadcrumb', [
          'pageTitle' => 'Login',
          'breadcrumbItems' => [
            ['label' => 'Home', 'href' => '/'],
            ['isActive' => true, 'label' => 'Login'],
          ]
        ]);
        ?>

        <div class="subsection row" style="margin-bottom: 130px;">
            <div class="col-xs-12">
                <?php if (Yii::app()->user->hasFlash('success-reset-password')) : ?>
                    <div class="alert alert-success">
                        <?php echo Yii::app()->user->getFlash('success-reset-password'); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-xs-6">
                <div class="subsection-login">
                    <p><?= Yii::t('app', 'Please fill out the following form with your login credentials:') ?></p>
                    <!-- inputs are already announced as required so screen readers do not need this message -->
                    <p aria-hidden="true"><?= Yii::t('app', 'Fields with <span class="symbol">*</span> are required.') ?></p>
                </div>

                <div class="login-div">
                    <? $form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => 'form-horizontal'))) ?>
                    <div class="form-group">
                        <label class="col-xs-3 control-label error required" for="LoginForm_username">
                            Email Address<span class="required" aria-hidden="true">*</span>
                        </label>
                        <div class="col-xs-9">
                            <?= $form->textField($model, 'username', array('size' => 50, 'class' => 'form-control', 'aria-describedby' => $model->getError('username') ? 'usernameError' : '', 'required' => true)) ?>
                            <div role="alert">
                                <?php echo $form->error($model, 'username', array('class' => 'form-error', 'id' => 'usernameError')); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-3 control-label error required" for="LoginForm_password">
                            Password<span class="required" aria-hidden="true">*</span>
                        </label>
                        <div class="col-xs-9">
                            <?= $form->passwordField($model, 'password', array('size' => 50, 'class' => 'form-control', 'aria-describedby' => $model->getError('password') ? 'passwordError' : '', 'required' => true)) ?>
                            <div role="alert">
                                <?php echo $form->error($model, 'password', array('class' => 'form-error', 'id' => 'passwordError')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-9 form-inverted-checkbox">
                            <?= $form->checkBox($model, 'rememberMe') ?>
                            <?= $form->label($model, 'rememberMe', array('disabled' => "disabled")) ?>
                        </div>
                    </div>
                    <hr aria-hidden="true">
                    <div class="button-div">
                        <?= CHtml::submitButton(Yii::t('app', 'Login'), array('class' => 'btn background-btn', 'style' => 'width:236px;')) ?>
                    </div>
                    <? $this->endWidget() ?>
                    <div class="login-links">
                        <?= CHtml::link(Yii::t('app', "Lost Password"), array('site/forgot')) ?>
                        <a href="/user/create">Create account</a>
                    </div>
                </div>
            </div>
        </div><!--login-->
</section>