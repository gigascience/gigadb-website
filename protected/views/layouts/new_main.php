<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js">
            </script>
        <![endif]-->
    <? if (Yii::app()->params['less_dev_mode']) { ?>
        <link rel="stylesheet/less" type="text/css" href="/less/site.less?time=<?= time() ?>">
        <? Yii::app()->clientScript->registerScriptFile('/js/less-1.3.0.min.js'); ?>
            <? } else { ?>
                <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css">
                <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
                <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
                <link rel="stylesheet" type="text/css" href="/fonts/open_sans/v13/open_sans.css">
                <link rel="stylesheet" type="text/css" href="/fonts/pt_sans/v8/pt_sans.css">
                <link rel="stylesheet" type="text/css" href="/fonts/lato/v11/lato.css">
                <!-- Disable common.css whilst fixing CSS problems -->
                <!-- <link rel="stylesheet" type="text/css" href="/css/common.css" /> -->
                <!-- Using current.css for developing fix for CSS problems in current green layout -->
                <link rel="stylesheet" type="text/css" href="/css/current.css" />
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js" defer></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                <?php if (isset($this->loadBaBbqPolyfills) && $this->loadBaBbqPolyfills) { ?>
                    <!-- Polyfills needed for 3.6.0/jquery.min.js and jquery.ba-bbq.min.js compatibility -->
                    <script src="https://code.jquery.com/jquery-migrate-3.3.2.min.js"></script>
                    <script>

// Limit scope pollution from any deprecated API
(function() {

var matched, browser;

// Use of jQuery.browser is frowned upon.
// More details: http://api.jquery.com/jQuery.browser
// jQuery.uaMatch maintained for back-compat
jQuery.uaMatch = function( ua ) {
    ua = ua.toLowerCase();

    var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
        /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
        /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
        /(msie) ([\w.]+)/.exec( ua ) ||
        ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
        [];

    return {
        browser: match[ 1 ] || "",
        version: match[ 2 ] || "0"
    };
};

matched = jQuery.uaMatch( navigator.userAgent );
browser = {};

if ( matched.browser ) {
    browser[ matched.browser ] = true;
    browser.version = matched.version;
}

// Chrome is Webkit, but Webkit is also Safari.
if ( browser.chrome ) {
    browser.webkit = true;
} else if ( browser.webkit ) {
    browser.safari = true;
}

jQuery.browser = browser;

jQuery.sub = function() {
    function jQuerySub( selector, context ) {
        return new jQuerySub.fn.init( selector, context );
    }
    jQuery.extend( true, jQuerySub, this );
    jQuerySub.superclass = this;
    jQuerySub.fn = jQuerySub.prototype = this();
    jQuerySub.fn.constructor = jQuerySub;
    jQuerySub.sub = this.sub;
    jQuerySub.fn.init = function init( selector, context ) {
        if ( context && context instanceof jQuery && !(context instanceof jQuerySub) ) {
            context = jQuerySub( context );
        }

        return jQuery.fn.init.call( this, selector, context, rootjQuerySub );
    };
    jQuerySub.fn.init.prototype = jQuerySub.fn;
    var rootjQuerySub = jQuerySub(document);
    return jQuerySub;
};

})();
                    </script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.ba-bbq/1.2.1/jquery.ba-bbq.min.js" defer></script>
                <?php } ?>
                <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js" defer></script>
                <? } ?>
                    <title>
                        <?php echo CHtml::encode($this->pageTitle); ?>
                    </title>
  <?= $this->renderPartial('//shared/_matomo') ?>
</head>

<body>
    <a href="#maincontent" class="skip-to-main-link">Skip to main content</a>
    <header>
        <div class="base-top-bar">
            <div class="container">
                <div class="row">
                    <nav aria-label="account" class="col-xs-5">
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
                                        <li>
                                            <a href="/site/admin">
                                                <?= Yii::t('app', 'Admin') ?>
                                            </a>
                                        </li>
                                        <? } ?>
                                            <li><a href="/site/logout"><i class="fa fa-sign-in"></i><?= Yii::t('app', 'LogOut') ?></a></li>
                                            <li><a href="/site/mapbrowse"><i class="fa fa-sign-in"></i><?=Yii::t('app' , "Browse Samples")?></a></li>
                                            <? } ?>
                        </ul>
                    </nav>
                    <div class="col-xs-7 clearfix top-bar-left">
                        <div class="search-bar clearfix">
                            <form action="/search/new" method="GET" role="search" class="search-form" aria-label="Datasets">
                                <?php
                                    $this->widget('application.components.DeferrableCJuiAutoComplete', array(
                                        'name' => 'keyword',
                                        'source' => array_values(array()),
                                        'options' => array(
                                            'minLength' => '2',
                                        ),
                                        'htmlOptions' => array(
                                            'aria-label'=>'Search GigaDB',
                                            'class' => 'search-input',
                                            'placeholder'=>'e.g. Chicken, brain, etc...',
                                        ),
                                    ));
                                    ?>
                                    <button class="btn-search" type="submit"><span class="fa fa-search"><span class="visually-hidden">Search</span></span>
                                    </button>
                            </form>
                        </div>
                        <ul class="share-zone clearfix">
                            <li>
                                <a class="fa fa-facebook" style="text-decoration: none;" href="http://facebook.com/GigaScience" title="GigaScience on Facebook" aria-label="GigaScience on Facebook"></a>
                            </li>
                            <li>
                                <a class="fa fa-twitter" style="text-decoration: none;" href="http://twitter.com/GigaScience" title="GigaScience on Twitter" aria-label="GigaScience on Twitter"></a>
                            </li>
                            <li>
                                <a class="fa fa-weibo" style="text-decoration: none;" href="http://weibo.com/gigasciencejournal" title="Gigascience on Weibo" aria-label="GigaScience on Weibo"></a>
                            </li>
                            <li>
                                <a class="fa fa-google-plus" style="text-decoration: none;" href="https://plus.google.com/u/0/104409890199786402308" title="GigaScience on Google+" aria-label="GigaScience on Google+"></a>
                            </li>
                            <li>
                                <a class="fa fa-rss" style="text-decoration: none;" href="http://gigasciencejournal.com/blog/" title="Gigascience Blog" aria-label="GigaScience Blog"></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="base-nav-bar">
            <div class="container">
                <div class="row">
                    <div class="col-xs-4">
                        <a href="/"><img src="/images/new_interface_image/logo.png" class="base-nav-logo-img" alt="GigaDB Logo and tagline: Revolutionizing data dissemination, organization and use"></a>
                    </div>
                    <nav aria-label="primary" class="col-xs-4 col-xs-offset-4">
                        <ul class="nav nav-pills main-nav-bar text-right">
                            <li><a href="/">Home</a></li>
                            <li class="dropdown">
                                <button class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="dropdown-toggle-label">
                                        About&nbsp;<i class="fa fa-angle-down" aria-hidden="true"></i>
                                    </span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="/site/about">General</a></li>
                                    <li><a href="/site/team">Our team</a></li>
                                    <li><a href="https://jobs.gigasciencejournal.com/">Jobs</a></li>
                                    <li><a href="/site/contact">Contact</a></li>
                                    <li><a href="/site/advisory">Advisory Board</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <button class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="dropdown-toggle-label">
                                        Help&nbsp;<i class="fa fa-angle-down" aria-hidden="true"></i>
                                    </span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="/site/help">Help</a></li>
                                    <li><a href="/site/faq">FAQ</a></li>
                                    <li><a href="/site/guide">Guidelines</a></li>
                                    <li><a href="https://stats.uptimerobot.com/LGVQXSkN1y">Systems Status</a></li>
                                </ul>
                            </li>
                            <li><a href="/site/term">Terms of use</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <main id="maincontent">
        <?php echo $content; ?>
    </main>
    <footer class="base-footer-bar">
        <div class="container">
            <div class="row">
                <div class="col-xs-4">
                    <ul class="list-inline base-footer-logo-bar">
                        <li><a href="http://gigasciencepress.com/"><img src="/images/new_interface_image/gigascience_white.png" alt="Go to GigaScience Journal web site"></a></li>
                    </ul>
                </div>
                <div class="col-xs-4">
                    <ul class="base-footer-email">
                        <li><a href="https://raw.githubusercontent.com/gigascience/gigadb-website/develop/CHANGELOG.md"><?php echo Yii::t('app' , 'Version: ' . Yii::app()->params["app_version"]) ?></a></li>
                    </ul>
                </div>
                <div class="col-xs-4 text-right">
                    <p class="base-footer-email"><a href="/site/contact"><i class="fa fa-envelope"></i> database@gigasciencejournal.com</a></p>
                    <ul class="list-inline base-footer-social-bar">
                        <li>
                            <a class="fa fa-facebook" style="text-decoration: none;" href="http://facebook.com/GigaScience" title="GigaScience on Facebook" aria-label="GigaScience on Facebook"></a>
                        </li>
                        <li>
                            <a class="fa fa-twitter" style="text-decoration: none;" href="http://twitter.com/GigaScience" title="GigaScience on Twitter" aria-label="GigaScience on Twitter"></a>
                        </li>
                        <li>
                            <a class="fa fa-weibo" style="text-decoration: none;" href="http://weibo.com/gigasciencejournal" title="Gigascience on Weibo" aria-label="GigaScience on Weibo"></a>
                        </li>
                        <li>
                            <a class="fa fa-google-plus" style="text-decoration: none;" href="https://plus.google.com/u/0/104409890199786402308" title="GigaScience on Google+" aria-label="GigaScience on Google+"></a>
                        </li>
                        <li>
                            <a class="fa fa-rss" style="text-decoration: none;" href="http://gigasciencejournal.com/blog/" title="Gigascience Blog" aria-label="GigaScience Blog"></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer -->
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
                         <img src="<?= Yii::app()->createAbsoluteUrl('/') . "/images/icons/id.png" ?>"/>&nbsp;&nbsp;<?= Yii::t('app', 'ORCID') ?>
                    </a>-->
            <a class="btn btnlog center giga-log" href="/site/login">
                        <img src="/images/icons/giga.png" alt="Login to GigaDB website">&nbsp;
                    </a>
        </div>
        <div class="content-btnlog">
            <a class="btn btnlog facebook-log" href="/opauth/facebook">
                <img src="/images/icons/fb.png" alt="Login with Facebook"/>&nbsp;&nbsp;<?=Yii::t('app' , 'Facebook')?>
            </a>
            <a class="btn btnlog google-log" href="/opauth/google">
                <img src="/images/icons/google.png" alt="Login with Google" />&nbsp;&nbsp;<?=Yii::t('app' , 'Google')?>
            </a>
        </div>
        <div class="content-btnlog">
            <a class="btn btnlog twitter-log" href="/opauth/twitter">
                    <img src="/images/icons/twi.png" alt="Login with twitter"/>&nbsp;&nbsp;<?=Yii::t('app' , 'Twitter')?>
            </a>
            <a class="btn btnlog linkedin-log" href="/opauth/linkedin">
                <img src="/images/icons/in.png" alt="Login with LinkedIn"/>&nbsp;&nbsp;<?=Yii::t('app' , 'LinkedIn')?>
            </a>
        </div>
    </div>
    <?php endif ?>
    <script type="application/ld+json">
    { "@context": "http://schema.org", "@type": "DataCatalog", "name": "GigaDB.org", "description": "GigaDB primarily serves as a repository to host data and tools associated with articles in GigaScience; however, it also includes a subset of datasets that are not associated with GigaScience articles. GigaDB defines a dataset as a group of files (e.g., sequencing data, analyses, imaging files, software programs) that are related to and support an article or study. Through our association with DataCite, each dataset in GigaDB will be assigned a DOI that can be used as a standard citation for future use of these data in other articles by the authors and other researchers. Datasets in GigaDB all require a title that is specific to the dataset, an author list, and an abstract that provides information specific to the data included within the set. We encourage detailed information about the data we host to be submitted by their creators in ISA-Tab, a format used by the BioSharing and ISA Commons communities that we work with to maintain the highest data and metadata standards in our journal. To maximize its utility to the research community, all datasets in GigaDB are placed under a CC0 waiver (for more information on the issues surrounding CC0 and data see Hrynaszkiewicz and Cockerill, 2012).Datasets that are not affiliated with a GigaScience article are approved for inclusion by the Editors of GigaScience. The majority of such datasets are from internal projects at the BGI, given their sponsorship of GigaDB. Many of these datasets may not have another discipline-specific repository suitably able to host them or have been rapidly released prior to any publications for use by the research community, whilst enabling their producers to obtain credit through data citation. The GigaScience Editors may also consider the inclusion of particularly interesting, previously unpublished datasets in GigaDB, especially if they meet our criteria and inclusion as Data Note articles in the journal.", "alternateName": "GigaScience Journal Database", "license": "Public Domain", "citation": "Tam P. Sneddon, Xiao Si Zhe, Scott C. Edmunds, Peter Li, Laurie Goodman, Christopher I. Hunter; GigaDB: promoting data dissemination and reproducibility, Database, Volume 2014, 1 January 2014, bau018, https://doi.org/10.1093/database/bau018", "url": "https://GigaDB.org/", "keywords": ["registry", "life science", "GigaScience Journal"], "provider": [{ "@type": "Person", "name": "GigaDB.org support", "email": "database@gigasciencejournal.com" }] }
    </script>
</body>

</html>
