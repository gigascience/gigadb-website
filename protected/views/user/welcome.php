<? $this->pageTitle = Yii::app()->name . ' - Welcome' ?>

<h2><?=Yii::t('app' , 'Welcome!')?></h2>
<div class="clear"></div>
<p><?=Yii::t('app' , 'Thank you for registering with GigaDB. An account activation email will be sent to your email address shortly. To complete your account\'s activation, please click on the activation link in the account activation email.')?><br/>
<?= Yii::t('app' , 'If you don\'t receive the email within a few minutes, please check your spam filters, or')?> <?= MyHtml::link(Yii::t('app' ,"resend the email"), array("user/sendActivationEmail", 'id'=>$user->id)) ?>.</p>

