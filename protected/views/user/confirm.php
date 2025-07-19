<div class="container">
  <? if ($user->is_activated) {
    $this->widget('TitleBreadcrumb', [
      'pageTitle' => 'Account activated',
      'breadcrumbItems' => [
        ['label' => 'Home', 'href' => '/'],
        ['isActive' => true, 'label' => 'Account activated'],
      ]
    ]);


    ?>
    <p>Your account has been activated. <?= CHtml::link("Log in", array('site/login')) ?> to configure your account.</p>
  <? } else {
    $this->widget('TitleBreadcrumb', [
      'pageTitle' => 'Account Pending',
      'breadcrumbItems' => [
        ['label' => 'Home', 'href' => '/'],
        ['isActive' => true, 'label' => 'Account Pending'],
      ]
    ]);


    ?>
    <p>You are now registered. We will contact you shortly. Feel free to <?= CHtml::link("contact us", "mailto:" . Yii::app()->params['support_email']) ?>&nbsp;if you prefer.</p>
  <? } ?>

</div>