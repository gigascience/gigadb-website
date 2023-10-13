<div class="container">

    <?php
    $this->widget('TitleBreadcrumb', [
        'pageTitle' => 'Manage Relations',
        'breadcrumbItems' => [
            ['label' => 'Datasets', 'href' => '/site/admin'],
            ['isActive' => true, 'label' => 'Manage'],
        ]
    ]);
    ?>

    <div class="mb-10">
        <a href="/adminRelation/create" class="btn background-btn">Create A New Relation</a>
    </div>
    <p>
        Column headers with links are sortable. Cells with a text input are used for filtering.
    </p>
    <?php $this->widget('CustomGridView', array(
        'id' => 'relation-grid',
        'dataProvider' => $model->search(),
        'itemsCssClass' => 'table table-bordered',
        'filter' => $model,
        'columns' => array(
            array('name' => 'doi_search', 'value' => '$data->dataset->identifier', 'headerHtmlOptions' => array('style' => 'width: 120px')),
            'related_doi',
            array('name' => 'relationship_name', 'value' => '$data->relationship->name'),
            CustomGridView::getDefaultActionButtonsConfig()
        ),
    )); ?>
</div>