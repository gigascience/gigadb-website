<?php $this->pageTitle=Yii::app()->name . ' - Reset Password' ?>
<h2><?=Yii::t('app' , 'Reset Password')?></h2>
<div class="row" id="login">
    <div class="span6 offset3">
    <p><?=Yii::t('app' , 'If you have lost your password, enter your email and we will send a new password to the email address associated with your account.')?></p>
        <div class="form form-horizontal well center">
            <?= MyHtml::form() ?>
            <label for='reset_user' class="required"><?=Yii::t('app' , 'Email')?> </label>
                <input name="reset_user" id="reset_user" type="text" value="" />
                <?= MyHtml::submitButton(Yii::t('app' , 'Reset'), array('class'=>'btn-green')) ?>
            </form>
        </div>
    </div>
</div><!--login-->
