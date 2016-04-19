<?php $this->pageTitle = Yii::app()->name . ' - Welcome' ?>

<h2><?=Yii::t('app' , 'Account Activation')?></h2>
<div class="clear"></div>

<div class="yiiForm">
<p><?=Yii::t('app' , 'An account activation email has been emailed to your email address.')?></p>
<p><?=Yii::t('app' , 'If you don\'t receive the email within a few minutes, please check your spam filters.')?> <br/>
<?=Yii::t('app' , 'Click')?> <?= MyHtml::link(Yii::t('app' ,"here"), array("user/sendActivationEmail", 'id'=>$user->id)) ?> <?=Yii::t('app' , 'to resend the email')?>
<?=Yii::t('app' , 'or') ?> <?= MyHtml::link(Yii::t('app' ,"contact us"), "mailto:" . Yii::app()->params['support_email']) ?>&nbsp;
<?=Yii::t('app' , 'and we will get it sorted.')?>
</p>
</div><!-- yiiForm -->
