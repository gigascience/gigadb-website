<?php
$this->pageTitle='Thanks';
?>
<div class="content">
    <div class="container">
        <div class="section page-title-section">
            <div class="page-title">
                <nav aria-label="breadcrumbs">
                    <ol class="breadcrumb pull-right">
                        <li><a href="/">Home</a></li>
                        <li class="active">Thanks</li>
                    </ol>
                </nav>
                <h4>Reset Password Request Submitted</h4>
            </div>
        </div>
        <div class="subsection" style="margin-bottom: 130px;">
            <p><?=Yii::t('app' , 'For security reasons, we cannot tell you if the email you entered is valid or not.<br/>If it is valid, we will send an email containing a link to where you can reset your password.')?></p>
            <p>If you do not receive the reset password email within a few minutes, please check your Junk/Spam E-mail folder</p>
            <p><?= CHtml::link(Yii::t('app' , "Contact us"), "mailto:".Yii::app()->params['support_email']) ?> <?=Yii::t('app' , 'if you are having problems and we will get it sorted out.')?></p>
        </div>
    </div>
</div>

