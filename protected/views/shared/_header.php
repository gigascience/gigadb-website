<a href="#maincontent" class="skip-to-main-link">Skip to main content</a>
<header>
    <?php
      $this->renderPartial('//shared/_topBar');
    ?>
    <div class="base-nav-bar">
        <div class="container">
            <div class="row">
                <div class="col-xs-8 col-md-4">
                    <a href="/"><img src="/images/new_interface_image/logo.png" class="base-nav-logo-img" alt="GigaDB Logo and tagline: Revolutionizing data dissemination, organization and use"></a>
                </div>
                <nav aria-label="primary" class="col-md-4 col-md-offset-4">
                    <button class="navbar-toggle" type="button" aria-controls="mobileNavigation" aria-expanded="false">
                        <i class="fa fa-bars fa-lg"></i>
                        <span class="sr-only">Toggle mobile navigation</span>
                    </button>
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
                            <button id="dropdown-help" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
<?php
$this->renderPartial('//shared/_mobile_navigation');

Yii::app()->assetManager->forceCopy = YII_DEBUG;
$jsDir = Yii::getAlias('/gigadb/app/client/js');
$jsUrl = Yii::app()->assetManager->publish($jsDir);
$jsScript = $jsUrl . '/mobile-navigation.js';

Yii::app()->clientScript->registerScriptFile(
    $jsScript,
    CClientScript::POS_END,
    ['type' => 'module', 'defer' => true]
);
?>
<script type="module">
  import { initMobileNavigation } from '<?php echo $jsScript; ?>';

  $(document).ready(function() {
      initMobileNavigation();
  });
</script>
