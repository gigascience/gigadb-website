<div class="base-top-bar">
  <div class="container">
    <div class="row">
      <nav aria-label="account" class="col-xs-12 col-md-5 account-navigation">
        <ul class="list-inline text-left base-top-account-bar">
          <?php if (Yii::app()->user->isGuest) { ?>
            <li><a href="/site/login"><i class="fa fa-sign-in"></i> Login / Signup</a></li>
          <?php } else {

            $name = Yii::app()->user->getFirstName();

            if (substr($name, -1) === 's') {

              $name = $name . '\'';
            } else {
              $name = $name . "'s";
            }
            ?>
            <li><a href="/user/view_profile"><i class="fa fa-sign-in"></i><?= Yii::t('app', $name . " GigaDB Page") ?></a>
            </li>
            <?php if (Yii::app()->user->checkAccess('admin')) { ?>
              <li>
                <a href="/site/admin">
                  <?= Yii::t('app', 'Admin') ?>
                </a>
              </li>
            <?php } ?>
            <li><a href="/site/logout"><i class="fa fa-sign-in"></i><?= Yii::t('app', 'LogOut') ?></a></li>
            <li><a href="/site/mapbrowse"><i class="fa fa-sign-in"></i><?= Yii::t('app', "Browse Samples") ?></a></li>
          <?php } ?>
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
                'aria-label' => 'Search GigaDB',
                'class' => 'search-input',
                'placeholder' => 'e.g. Chicken, brain, etc...',
                'id' => 'desktopSearchbar',
              ),
            ));
            ?>
            <button class="btn-search" type="submit"><span class="fa fa-search"><span
                  class="visually-hidden">Search</span></span>
            </button>
          </form>
        </div>
        <ul class="share-zone clearfix icon-list">
          <li>
            <a class="fa fa-facebook" style="text-decoration: none;" href="http://facebook.com/GigaScience"
              title="GigaScience on Facebook" aria-label="GigaScience on Facebook"></a>
          </li>
          <li class="icon-list-item">
            <a href="http://x.com/GigaScience" title="GigaScience on X" class="icon-list-item__link"
              aria-label="GigaScience on X">
              <img class="icon-list-item__image" src="/images/icons/x-logo.svg" alt="">
            </a>
          </li>
          <li class="icon-list-item">
            <a href="https://bsky.app/profile/gigascience.bsky.social" title="GigaScience on BlueSky" class="icon-list-item__link" target="_blank" rel="noopener noreferrer" aria-label="GigaScience on BlueSky">
              <img class="icon-list-item__image" src="/images/icons/bsky-logo.svg" alt="">
            </a>
          </li>
          <li>
            <a class="fa fa-weibo" style="text-decoration: none;" href="http://weibo.com/gigasciencejournal"
              title="Gigascience on Weibo" aria-label="GigaScience on Weibo"></a>
          </li>
          <li class="icon-list-item">
            <a href="https://mastodon.social/@GigaScience" title="GigaScience on Mastodon" class="icon-list-item__link"
              aria-label="GigaScience on Mastodon">
              <img class="icon-list-item__image" src="/images/icons/mastodon-logo.svg" alt="">
            </a>
          </li>
          <li>
            <a class="fa fa-rss" style="text-decoration: none;" href="http://gigasciencejournal.com/blog/"
              title="Gigascience Blog" aria-label="GigaScience Blog"></a>
          </li>
        </ul>
      </div>
    </div>
    <div class="row search-bar-mobile">
      <div class="col-xs-12">
        <form action="/search/new" method="GET" role="search" class="search-form" aria-label="Datasets">
          <?php
          $this->widget('application.components.DeferrableCJuiAutoComplete', array(
            'name' => 'keyword',
            'source' => array_values(array()),
            'options' => array(
              'minLength' => '2',
            ),
            'htmlOptions' => array(
              'aria-label' => 'Search GigaDB',
              'class' => 'search-input',
              'placeholder' => 'e.g. Chicken, brain, etc...',
              'id' => 'mobileSearchbar',
            ),
          ));
          ?>
          <button class="btn-search" type="submit">
            <span class="fa fa-search"><span class="sr-only">Search</span></span>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>