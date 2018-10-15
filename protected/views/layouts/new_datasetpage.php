<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js">
            </script>
        <![endif]-->
    <? if (Yii::app()->params['less_dev_mode']) { ?>
        <link rel="stylesheet/less" type="text/css" href="/less/site.less?time=<?= time() ?>">
        <? Yii::app()->clientScript->registerScriptFile('/js/less-1.3.0.min.js'); ?>
    <? } else { ?>
        <link rel="stylesheet" type="text/css" href="http://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="http://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="/fonts/open_sans/v13/open_sans.css">
        <link rel="stylesheet" type="text/css" href="/fonts/pt_sans/v8/pt_sans.css">
        <link rel="stylesheet" type="text/css" href="/fonts/lato/v11/lato.css">
        <link rel="stylesheet" type="text/css" href="/css/common.css"/>
          <style type="text/css">
            table.dataTable {
                margin: 0px 0px 40px 0px !important;
            }
            table.dataTable thead th,
            table.dataTable tbody td,
            table.dataTable thead > tr > th.sorting_asc, 
            table.dataTable thead > tr > th.sorting_desc, 
            table.dataTable thead > tr > th.sorting, 
            table.dataTable thead > tr > td.sorting_asc, 
            table.dataTable thead > tr > td.sorting_desc, 
            table.dataTable thead > tr > td.sorting {
                padding: 8px;
                background-image: none;
            }
            table.dataTable thead .sorting::after, 
            table.dataTable thead .sorting_asc::after, 
            table.dataTable thead .sorting_desc::after, 
            table.dataTable thead .sorting_asc_disabled::after, 
            table.dataTable thead .sorting_desc_disabled::after {
                content: none;
            }
            table.dataTable.no-footer {
                border-bottom: 1px solid #ddd;
            }
            .dataTables_wrapper .dataTables_length {
                color: #656565;
                margin-bottom: 20px;
            }
            .dataTables_wrapper .dataTables_length label {
                margin: 0px;
            }
            .dataTables_wrapper .dataTables_length select {
                appearance:none;  
                -moz-appearance:none;  
                -webkit-appearance:none;
                height: 34px;
                line-height: 20px;
            }
            .dataTables_wrapper .dataTables_length select::-ms-expand {
                display: none;
            }
            div.dataTables_wrapper div.dataTables_paginate {
                padding: 0px;
                float: none;
                text-align: center;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0px;
                border: 0px;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                border: 0px;
                background: none;
            }
            div.dataTables_wrapper div.dataTables_paginate ul.pagination {
                margin: 0px 10px 0px 0px;
                vertical-align: top;
            }
        </style>
        <script type="text/javascript" src="http://cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
        <script type="text/javascript" src="http://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <? } ?>

    <?= $this->renderPartial('//shared/_google_analytics')?>

    <title><?php echo MyHtml::encode($this->pageTitle); ?></title>
</head>

<body>
    
            <div class="base-top-bar">
            <div class="container">
                <div class="row">
                     <div class="col-xs-5">
                        <ul class="list-inline text-left base-top-account-bar">
                             <? if(Yii::app()->user->isGuest) { ?>
                            <li><a href="/site/login"><i class="fa fa-sign-in"></i> Login / Signup</a></li>                         
                             <? } else { 
                
                        $name = Yii::app()->user->getFirst_Name();
                      
                // var_dump($name);

                        if (substr($name, -1) === 's') {

                            $name = $name . '\'';
                        } else {
                            $name = $name . "'s";
                        }
                ?>
                <li><a href="/user/view_profile"><i class="fa fa-sign-in"></i><?= Yii::t('app', $name . " GigaDB Page") ?></a></li>
                            <? if (Yii::app()->user->checkAccess('admin')) { ?>
                <li><a href="/site/admin"><?= Yii::t('app', 'Admin') ?></a></li>
                            <? } ?>
                <li><a href="/site/logout"><i class="fa fa-sign-in"></i><?= Yii::t('app', 'LogOut') ?></a></li>
                <li><a href="/site/mapbrowse"><i class="fa fa-sign-in"></i><?=Yii::t('app' , "Browse Samples")?></a></li>
                            <? } ?>       
                </ul>
                    </div>
                    <div class="col-xs-7 clearfix">
                        <ul class="share-zone clearfix">
                            <li><a href="http://facebook.com/GigaScience" title="Facebook"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="http://twitter.com/GigaScience" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="http://weibo.com/gigasciencejournal" title="Weibo"><i class="fa fa-weibo"></i></a></li>
                            <li><a href="https://plus.google.com/u/0/104409890199786402308" title="Google+"><i class="fa fa-google-plus"></i></a></li>
                            <li><a href="http://gigasciencejournal.com/blog/" title="GigaBlog"><i class="fa fa-rss"></i></a></li>
                        </ul>
                        <div class="search-bar clearfix">
                            <form action="/search/new" method="GET">    
                            <?php        
               
                                $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                                'name'=>'keyword', 
                                'source'=> array_values(array()),
                                
                                'options'=>array(
                                'minLength'=>'2',
                                    ),
                                'htmlOptions'=>array(
                                 'class'=>'search-input',
                                 'placeholder'=>'e.g. Chicken, brain etc...',   
                                    ),
                                 ));
       
       
                            ?>
                            <button class="btn-search" type="submit"><span class="fa fa-search"></span></button>
                            </form>
                         
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
            <div class="base-nav-bar">
            <div class="container">
                <div class="row">
                    <div class="col-xs-4">
                        <a href="/"><img src="/images/new_interface_image/logo.png" class="base-nav-logo-img"></a>
                    </div>
                    <div class="col-xs-4 col-xs-offset-4">
                        <ul class="nav nav-pills main-nav-bar text-right">
                            <li><a href="/">Home</a></li>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                    About&nbsp;<i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="/site/about">General</a></li>
                                    <li><a href="/site/team">Our team</a></li>
                                    <li><a href="/site/contact">Contact</a></li>
                                    <li><a href="/site/advisory">Advisory Board</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                 <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                    Help&nbsp;<i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu">
                                <li><a href="/site/help">Help</a></li>
                                <li><a href="/site/faq">FAQ</a></li>
                                </ul>
                             </li>    
                            <li><a href="/site/term">Terms of use</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


<!--
    <?php if(isset($this->breadcrumbs)):?>
        <?php $this->widget('bootstrap.widgets.BootBreadcrumbs', array(
            'links'=>$this->breadcrumbs,
        )); ?>
    <?php endif?>
-->


    <?php echo $content; ?>

<div class="base-footer-bar">
            <div class="container">
                <div class="row">
                    <div class="col-xs-6">
                        <ul class="list-inline base-footer-logo-bar">
                            <li><a href="https://academic.oup.com/gigascience"><img src="/images/new_interface_image/gigascience.png"></a></li>
                            <li><a href="http://www.genomics.cn/"><img src="/images/new_interface_image/bgi-logo.png"></a></li>
                            <li><a href="https://www.cngb.org"><img src="/images/new_interface_image/chinagenbank.png"></a></li>
                        </ul>
                    </div>
                    <div class="col-xs-6 text-right">
                        <p class="base-footer-email"><a href="/site/contact"><i class="fa fa-envelope"></i> database@gigasciencejournal.com</a></p>
                        <ul class="list-inline base-footer-social-bar">
                            <li><a href="http://facebook.com/GigaScience" title="Facebook"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="http://twitter.com/GigaScience" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="http://weibo.com/gigasciencejournal" title="Weibo"><i class="fa fa-weibo"></i></a></li>
                            <li><a href="https://plus.google.com/u/0/104409890199786402308" title="Google+"><i class="fa fa-google-plus"></i></a></li>
                            <li><a href="http://gigasciencejournal.com/blog/" title="RSS"><i class="fa fa-rss"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div><!-- footer -->
    <!-- Le javascript
     ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
     <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
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
</html>
