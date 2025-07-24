<div id="mobileNavigation" class="mobile-navigation">
  <button type="button" class="mobile-navigation__close" aria-label="Close">
    <i class="fa fa-times"></i>
  </button>
  <div class="mobile-navigation__content">
    <div class="mobile-navigation__logo">
      <img src="/images/new_interface_image/logo.png" alt="GigaDB Logo" class="mobile-navigation__logo-image">
    </div>
    <nav class="mobile-navigation__nav" aria-label="Mobile Navigation">
      <!-- Account Navigation -->
      <div class="mobile-navigation__group">
        <?php if (Yii::app()->user->isGuest) { ?>
          <a href="/site/login" class="mobile-navigation__link"><i class="fa fa-sign-in"></i> Login / Signup</a>
        <? } else {
          $name = Yii::app()->user->getFirst_Name();
          if (substr($name, -1) === 's') {
            $name = $name . '\'';
          } else {
            $name = $name . "'s";
          }
        ?>
          <a href="/user/view_profile" class="mobile-navigation__link"><i class="fa fa-sign-in"></i><?= Yii::t('app', $name . " GigaDB Page") ?></a>
          <? if (Yii::app()->user->checkAccess('admin')) { ?>
            <a href="/site/admin" class="mobile-navigation__link"><?= Yii::t('app', 'Admin') ?></a>
          <? } ?>
          <a href="/site/logout" class="mobile-navigation__link"><i class="fa fa-sign-in"></i><?= Yii::t('app', 'LogOut') ?></a>
          <a href="/site/mapbrowse" class="mobile-navigation__link"><i class="fa fa-sign-in"></i><?= Yii::t('app', "Browse Samples") ?></a>
        <? } ?>
      </div>

      <div class="mobile-navigation__group">
        <a href="/" class="mobile-navigation__link">Home</a>
      </div>

      <!-- About section -->
      <div class="mobile-navigation__group">
        <a href="/site/about" class="mobile-navigation__link">General</a>
        <a href="/site/team" class="mobile-navigation__link">Our team</a>
        <a href="https://jobs.gigasciencejournal.com/" class="mobile-navigation__link">Jobs</a>
        <a href="/site/contact" class="mobile-navigation__link">Contact</a>
        <a href="/site/advisory" class="mobile-navigation__link">Advisory Board</a>
      </div>

      <!-- Help section -->
      <div class="mobile-navigation__group">
        <a href="/site/help" class="mobile-navigation__link">Help</a>
        <a href="/site/faq" class="mobile-navigation__link">FAQ</a>
        <a href="/site/guide" class="mobile-navigation__link">Guidelines</a>
        <a href="https://stats.uptimerobot.com/LGVQXSkN1y" class="mobile-navigation__link">Systems Status</a>
      </div>

      <div class="mobile-navigation__group">
        <a href="/site/term" class="mobile-navigation__link">Terms of use</a>
      </div>

      <!-- Social Media Links -->
      <div class="mobile-navigation__group mobile-navigation__social">
        <div class="social-icons">
          <a href="http://facebook.com/GigaScience" class="social-icon" title="GigaScience on Facebook" aria-label="GigaScience on Facebook">
            <i class="fa fa-facebook"></i>
          </a>
          <a href="http://x.com/GigaScience" class="social-icon" title="GigaScience on X" aria-label="GigaScience on X">
            <img src="/images/icons/x-logo.svg" alt="" class="x-icon">
          </a>
          <a href="https://bsky.app/profile/gigascience.bsky.social" class="social-icon" title="GigaScience on BlueSky" aria-label="GigaScience on BlueSky">
            <img src="/images/icons/bsky-logo.svg" alt="" class="bsky-icon">
          </a>
          <a href="http://weibo.com/gigasciencejournal" class="social-icon" title="Gigascience on Weibo" aria-label="GigaScience on Weibo">
            <i class="fa fa-weibo"></i>
          </a>
          <a href="https://mastodon.social/@GigaScience" class="social-icon" title="GigaScience on Mastodon" aria-label="GigaScience on Mastodon">
            <img src="/images/icons/mastodon-logo.svg" alt="" class="mastodon-icon">
          </a>
          <a href="http://gigasciencejournal.com/blog/" class="social-icon" title="Gigascience Blog" aria-label="GigaScience Blog">
            <i class="fa fa-rss"></i>
          </a>
        </div>
      </div>
    </nav>
  </div>
</div>