<div class='container'>
    <?php
    $this->widget('TitleBreadcrumb', [
        'pageTitle'       => 'Manage External Link Types',
        'breadcrumbItems' => [
            ['label' => 'Admin', 'href' => '/site/admin'],
            ['isActive' => true, 'label' => 'Manage'],
        ]
    ]);
    ?>

    <div class="mb-10">
        <a href="/adminExternalLinkType/create" class="btn background-btn">Create a New External Link Type</a>
    </div>
    <p>
        Column headers with links are sortable. Cells with a text input are used for filtering.
    </p>

    <?php $this->widget('CustomGridView', array(
        'id'            => 'external-link-type-grid',
        'dataProvider'  => $model->search(),
        'itemsCssClass' => 'table table-bordered',
        'filter'        => $model,
        'columns'       => array(
            'name',
            'description',
            CustomGridView::getDefaultActionButtonsConfig()
        ),
    )); ?>
</div>
