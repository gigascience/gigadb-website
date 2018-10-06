<?php $this->pageTitle=Yii::app()->name . ' - Password Reset' ?>

<h2><?=Yii::t('app' , 'Password Reset')?></h2>
<div class="clear"></div>
<p><?=Yii::t('app' , 'For security reasons, we cannot tell you if the email you entered is valid or not.<br/>If it is valid, we will send new password.')?></p>
    <p>If you do not receive the reset password email within a few minutes, please check your Junk/Spam E-mail folder</p>
    <p><?= CHtml::link(Yii::t('app' , "Contact us"), "mailto:" .
    Yii::app()->params['support_email']) ?> <?=Yii::t('app' , 'if you are having problems and we will get it sorted out.')?></p>
    

