<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />

    <?php if ($metaData['private']) : ?>
        <meta name="robots" content="noindex">
        <meta name="googlebot" content="noindex">
    <?php endif ?>

    <? if ($metaData['redirect']) {
            Yii::app()->clientScript->registerMetaTag("5;url={$metaData['redirect']}", null, 'refresh');
        }
    ?>

    <meta name="title" content="<?php echo CHtml::encode($this->pageTitle); ?>" />
    <meta name="description" content="<?php echo CHtml::encode($metaData['description']) ?>" />
    <meta name="identifier-url" content="<?php echo Yii::app()->createAbsoluteUrl(Yii::app()->request->url) ?>">
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js">
            </script>
        <![endif]-->
    <? if (Yii::app()->params['less_dev_mode']) { ?>
        <link rel="stylesheet/less" type="text/css" href="/less/site.less?time=<?= time() ?>">
        <? Yii::app()->clientScript->registerScriptFile('/js/less-1.3.0.min.js'); ?>
    <? } else { ?>
        <link rel="stylesheet" type="text/css" href="/css/site.css"/>
    <? } ?>

    <?= $this->renderPartial('//shared/_google_analytics')?>

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>

    <?php
        $url=Yii::app()->createAbsoluteUrl(Yii::app()->request->url);
        $split_url=explode("view/id/",$url);
        $canon_url=$split_url[0];
        if (count($split_url)>1){
            $second_split=explode("/",$split_url[1]);
            $canon_url=$canon_url.$second_split[0];
        }
    ?>
    <link rel="canonical" href="<?php echo $canon_url; ?>" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>

<body>
<header>

        <div class="container">
            <h1><a href="/site/index" id="logo" title="GigaDB"><img src="/images/logo.jpg" /></a></h1>
            <div class="navbar">
                <ul class="nav pull-right navigation">
                <li class="<? if(Yii::app()->controller->action->id=='index') echo "active"; ?>"><a href="/site/index"><?=Yii::t('app' , 'Home')?></a>|</li>
                <li class="<? if(Yii::app()->controller->action->id=='about') echo "active"; ?>"><a href="/site/about"><?=Yii::t('app' , 'About')?></a>|</li>
                <li class="<? if(Yii::app()->controller->action->id=='contact') echo "active"; ?>"><a href="/site/contact"><?=Yii::t('app' , 'Contact')?></a>|</li>
                <li class="<? if(Yii::app()->controller->action->id=='term') echo "active"; ?>"><a href="/site/term"><?=Yii::t('app' , 'Terms of use')?></a></li>
                </ul>
            </div>
            <p>
                <a class="btn" href="/site/help"><?=Yii::t('app' , 'Help')?></a>
                <?php if(Yii::app()->user->isGuest) { ?>
                <a class="btn" href="/site/login"><?=Yii::t('app' , 'Login')?></a>
                <a class="btn" href="/site/mapbrowse"><?=Yii::t('app' , "Browse Samples")?></a> 
		<a class="btn" href="/user/create" id="btnCreateAccount" title="<?=Yii::t('app' , 'An account with GigaDB is required if you want to upload a dataset or be automatically notified of new content of interest to you')?>"><?=Yii::t('app' , 'Create account')?></a>
                <?php } else {

                        $name = Yii::app()->user->getFirst_Name();

                // var_dump($name);

                        if (substr($name, -1) === 's') {

                            $name = $name . '\'';
                        } else {
                            $name = $name . "'s";
                        }
                ?>
                <a class="btn" href="/user/view_profile"><?=Yii::t('app' ,$name. " GigaDB Page")?></a>
                    <?php if (Yii::app()->user->checkAccess('admin')) { ?>
                    <a class="btn" href="/site/admin"><?=Yii::t('app' , 'Administration')?></a>
                    <?php } ?>
                    <a class="btn" href="/site/logout"><?=Yii::t('app' , 'LogOut')?></a>
                <?php } ?>
            </p>
        </div>
</header>


<div class="container" id="wrap">
    <?php echo $content; ?>
</div>
<footer id="footer">
    <div class="container">
        <div class="pull-left">
            <a  class="pull-left" title="(Giga)nScience" href="http://www.gigasciencejournal.com/"><img src="/images/gigascience.png" height="32" alt="GigaScience"/></a>
            <a  class="pull-left footer-logo" title="BGI" href="http://en.genomics.cn/navigation/index.action"><img src="/images/bgi-logo.png" height="32" alt="BGI"/></a>
            <a  class="pull-left footer-logo" title="China National Genebank" href="http://www.nationalgenebank.org/"><img src="/images/chinagenbank.png" height="32" alt="China National Genebank"/></a>
        </div>
        <div class="navbar">
            <ul class="nav">
            <li class="<? if(Yii::app()->controller->action->id=='index') echo "active"; ?>"><a href="/site/index"><?=Yii::t('app' , 'Home')?></a>|</li>
                <?php if(Yii::app()->user->isGuest) { ?>
                <li class="<? if(Yii::app()->controller->action->id=='login') echo "active"; ?>"><a href="/site/chooseLogin"><?=Yii::t('app' , 'Login')?></a>|</li>
                <?php } else { ?>
                <li class="<? if(Yii::app()->controller->action->id=='view_profile') echo "active"; ?>"><a href="/user/view_profile"><?=Yii::t('app' , 'My GigaDB Page')?></a>|</li>
                <?php } ?>
                <li class="<? if(Yii::app()->controller->action->id=='about') echo "active"; ?>"><a href="/site/about"><?=Yii::t('app' , 'About')?></a>|</li>
                <li class="<? if(Yii::app()->controller->action->id=='contact') echo "active"; ?>"><a href="/site/contact"><?=Yii::t('app' , 'Contact')?></a>|</li>
                <li class="<? if(Yii::app()->controller->action->id=='term') echo "active"; ?>"><a href="/site/term"><?=Yii::t('app' , 'Terms of use')?></a></li>
            </ul>
        </div>
        <div class="pull-right">
            <ul class="social-links">
            <li id="share_facebook"><a href="http://facebook.com/GigaScience"><?=Yii::t('app' , 'Be a fan on Facebook')?></a></li>
            <li id="share_twitter"><a href="http://twitter.com/GigaScience"><?=Yii::t('app' , 'Follow us on Twitter')?></a></li>
            <li id="share_weibo"><a href="http://weibo.com/gigasciencejournal"><?=Yii::t('app' , 'Follow us on Sina')?></a></li>
            <li id="share_google"><a href="https://plus.google.com/u/0/104409890199786402308"><?=Yii::t('app' , 'Follow us on Google+')?></a></li>
            <li id="share_rss"><a href="http://blogs.openaccesscentral.com/blogs/gigablog/"><?=Yii::t('app' , 'GigaBlog')?></a></li>
            </ul>
        </div>
    </div>
</footer><!-- footer -->
    <!-- Le javascript
     ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
     <script type="text/javascript" src="https://platform.twitter.com/widgets.js"></script>
     <!-- <script src="/js/jquery.js"></script>-->
     <!-- <script src="/js/google-code-prettify/prettify.js"></script>-->
     <!-- <script src="/js/bootstrap-transition.js"></script>
     <script src="/js/bootstrap-alert.js"></script>
     <script src="/js/bootstrap-modal.js"></script>
     <script src="/js/bootstrap-dropdown.js"></script>
     <script src="/js/bootstrap-scrollspy.js"></script>
     <script src="/js/bootstrap-tab.js"></script>
     <script src="/js/bootstrap-tooltip.js"></script>
     <script src="/js/bootstrap-popover.js"></script>
     <script src="/js/bootstrap-button.js"></script>
     <script src="/js/bootstrap-collapse.js"></script>
     <script src="/js/bootstrap-carousel.js"></script>
     <script src="/js/bootstrap-typeahead.js"></script>-->
     <!-- <script src="/js/application.js"></script>-->

     <?php if (Yii::app()->user->isGuest) : ?>

        <div class="popover-login" style="display: none;">
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
        </div>

     <?php endif ?>

     <script>
        $(function() {
            $("#btnCreateAccount").tooltip({'placement':'left'});

            <?php if (Yii::app()->user->isGuest) : ?>
            $('#btnLogin').attr('data-content', $('.popover-login').html()).popover({
                trigger: 'manual',
                animate: false,
                stay: true,
                placement: 'bottom',
                template: '<div class="popover login-popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
            }).on('click', function() {
                return false;
            }).mouseenter(function(e) {
                $(this).popover('show');

                $('.popover').one('mouseleave', function() {
                    $('#btnLogin').popover('hide');
                });
            });
        <?php endif ?>
        });
    </script>
</body>
<!--HYPOTHES.IS CODE. Line 1 adds hypothesis sidebar to site, line 2 sets highlighting to automatically be displayed.-->
<script async defer src="//hypothes.is/embed.js"></script>
<script>window.hypothesisConfig=function(){return{showHighlights:true}};</script>


</html>
