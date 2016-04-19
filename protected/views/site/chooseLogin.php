<h4><?= Yii::t('app', 'Sign in using your account with') ?></h4>
<div class="content-btnlog">
    <!--<a class="btn btnlog orcid-log" href="/opauth/orcid">
         <img src="<?= Yii::app()->createAbsoluteUrl('/') .  "/images/icons/id.png" ?>"/>&nbsp;&nbsp;<?=Yii::t('app' , 'ORCID')?>
    </a>-->
    <a class="btn btnlog center giga-log" href="/site/login">
         <img src="/images/icons/giga.png"/>&nbsp;
    </a>
 </div>
 <div class="content-btnlog">
     <a class="btn btnlog facebook-log" href="/opauth/facebook">
         <img src="/images/icons/fb.png"/>&nbsp;&nbsp;<?=Yii::t('app' , 'Facebook')?>
     </a>
    <a class="btn btnlog google-log" href="/opauth/google">
         <img src="/images/icons/google.png"/>&nbsp;&nbsp;<?=Yii::t('app' , 'Google')?>
    </a>
 </div>
 <div class="content-btnlog">
    <a class="btn btnlog twitter-log" href="/opauth/twitter">
         <img src="/images/icons/twi.png"/>&nbsp;&nbsp;<?=Yii::t('app' , 'Twitter')?>
    </a>
    <a class="btn btnlog linkedin-log" href="/opauth/linkedin">
        <img src="/images/icons/in.png"/>&nbsp;&nbsp;<?=Yii::t('app' , 'LinkedIn')?>
    </a>
 </div>