<div class="title-bar">
    <?php echo CHtml::tag($pageTitleLevel, ['class' => 'h4 title title-bar-title', 'id' => $pageTitleId], $pageTitle); ?>
    <nav aria-label="title-bar-breadcrumb-nav">
        <ol class="title-bar-breadcrumb-list">
            <?= $breadcrumbHtml ?>
        </ol>
    </nav>
</div>