<div class="section page-title-section">
    <div class="page-title">
        <nav aria-label="breadcrumbs">
          <ol class="breadcrumb pull-right">
              <?= $breadcrumbHtml ?>
          </ol>
        </nav>
        <?php echo CHtml::tag($pageTitleLevel, ['class' => 'h4', 'id' => $pageTitleId], $pageTitle); ?>
    </div>
</div>