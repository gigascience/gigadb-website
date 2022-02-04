<div class="content">
    <div class="container">
        <section class="page-title-section">
            <div class="page-title">
                <ol class="breadcrumb pull-right">
                    <li><a href="/">Home</a></li>
                    <li class="active">Reset</li>
                </ol>
                <h4>Reset Password</h4>
            </div>
        </section>
    <div class="subsection" style="margin-bottom: 130px;">
        <p>Fields with <span class="symbol">*</span> are required.</p>
            <div class="reset-message-div">
                <p>
                    If you have lost your password, enter your email and we will send a new password to the email address associated with your account.
                </p>
            </div>
            <div class="create-div">
                <? $form=$this->beginWidget('CActiveForm', array(
                    'id'=>'reset-password-form',
                    'enableAjaxValidation'=>false,
                    'htmlOptions'=>array('class'=>'form-horizontal')
                )) ?>
                <div class="form-group">
                    <label class="col-xs-2 control-label required" for="User_email">Email <span class="required">*</span></label>
                    <div class="col-xs-8">
                        <input class="form-control" name="LostUserPassword[email]" id="User_email" type="text" maxlength="128" value="">
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
