<div class="container">
  <? if ($user->is_activated) { ?>
    <h1 class="h2">Your account has been activated</h1>

    <p><?= CHtml::link("Log in", array('site/login')) ?> to configure your account.</p>
    <? } else { ?>
    <h1 class="h2">Account Pending</h1>

    <p>You are now registered. We will contact you shortly. Feel free to <?= CHtml::link("contact us", "mailto:database@gigasciencejournal.com" . Yii::app()->params['support_email']) ?> if you prefer.</p>
    <? } ?>
</div>

