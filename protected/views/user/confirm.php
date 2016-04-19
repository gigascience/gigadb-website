<? if ($user->is_activated) { ?>
<h3>Your account has been activated</h3>

<p><?= MyHtml::link("Log in", array('site/login')) ?> to configure your account.</p>
<? } else { ?>
<h3>Account Pending</h3>

You are now registered. We will contact you shortly. Feel free to
or <?= MyHtml::link("contact us", "mailto:" . Yii::app()->params['support_email']) ?>&nbsp;
if you prefer.</p>
<? } ?>

