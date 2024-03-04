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