<h1>Change password form</h1>
<div class="content">
    <div class="container">
        <section class="page-title-section">
            <div class="page-title">
                <ol class="breadcrumb pull-right">
                    <li><a href="/">Home</a></li>
                    <li class="active">Change Password</li>
                </ol>
                <h4><?= Yii::t('app', 'Change Password') ?></h4>
            </div>
        </section>
    </div>
</div>

<div class="clear"></div>
<div class="container">
    <div class="row">
        <div class="col-xs-8 col-xs-offset-2">
            <div class="form well user-profile-box">
                <div class="panel-body">

                    <?php
                    $form = $this->beginWidget('CActiveForm', array(
                        'id' => 'ChangePassword-form',
                        'enableAjaxValidation' => false,
                        'htmlOptions' => array('class' => 'form-horizontal'),
                    ));
                    ?>

                        <?= isset($error) && $error ? '<div class="row">' . $error . '</div>' : '' ?>

                    <div class="form-group">
                            <?php echo $form->labelEx($model, 'password', array('class' => 'col-xs-5 control-label')); ?>
                        <div class="col-xs-5">
<?php echo $form->passwordField($model, 'password', array('size' => 60, 'maxlength' => 128, 'class' => 'form-control', 'value' => "")); ?>
<?php echo $form->error($model, 'password'); ?>
                        </div>
                    </div>


                    <div class="form-group">
                            <?php echo $form->labelEx($model, 'confirmPassword', array('class' => 'col-xs-5 control-label')); ?>
                        <div class="col-xs-5">
<?php echo $form->passwordField($model, 'confirmPassword', array('size' => 60, 'maxlength' => 128, 'class' => 'form-control')); ?>
<?php echo $form->error($model, 'confirmPassword'); ?>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="/user/view_profile" class="btn background-btn"><?= Yii::t('app', 'Cancel') ?></a>
                    <?php echo CHtml::submitButton(Yii::t('app', 'Save'), array('class' => 'btn background-btn')); ?>
                    </div>
                    <div class="clear"></div>
<?php $this->endWidget(); ?>

                </div><!--span8-->
            </div>
        </div>
    </div>
</div><!-- user-form -->
<br>
<br>
