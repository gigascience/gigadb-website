<? $this->pageTitle = Yii::app()->name . ' - Welcome' ?>

<div class="container">
  <?php
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => Yii::t('app', 'Welcome!'),
    'breadcrumbItems' => [
      ['label' => 'Home', 'href' => '/'],
      ['isActive' => true, 'label' => 'Welcome'],
    ]
  ]);
  ?>
  <p>
    <?= Yii::t('app', 'Thank you for registering with GigaDB. An account activation email will be sent to your email address shortly. To complete your account\'s activation, please click on the activation link in the account activation email.') ?><br />
    <?= Yii::t('app', 'If you don\'t receive the email within a few minutes, please check your spam filters, or') ?>
    <?= CHtml::link(Yii::t('app', "resend the email"), array("user/sendActivationEmail", 'id' => $user->id)) ?>.
  </p>
</div>