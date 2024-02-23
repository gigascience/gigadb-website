<div class="section page-title-section">
    <div class="page-title">
        <nav aria-label="<?php echo $navLabel; ?>">
          <ol class="breadcrumb pull-right">
              <?php echo $breadcrumbHtml ?>
          </ol>
        </nav>
        <?php echo CHtml::tag($pageTitleLevel, ['class' => 'h4', 'id' => $pageTitleId], $pageTitle); ?>
    </div>
</div>