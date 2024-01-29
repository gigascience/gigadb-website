<div class="container">
    <?php
    $this->widget('TitleBreadcrumb', [
        'pageTitle' => 'Manage Curation Log',
        'breadcrumbItems' => [
            ['label' => 'Admin', 'href' => '/site/admin'],
            ['isActive' => true, 'label' => 'Manage'],
        ]
    ]);

    $this->widget(
        'CustomGridView',
        [
            'id'            => 'dataset-grid',
            'dataProvider'  => $model->search(),
            'itemsCssClass' => 'table table-bordered',
            'columns'       => [
                'id',
                'dataset_id',
                array('name' => 'doi', 'value' => '$data->dataset->identifier'),
                'creation_date',
                'created_by',
                'action',
                'comments',
                'last_modified_date',
                'last_modified_by',
                CustomGridView::getDefaultActionButtonsConfig()
            ],
        ]
    );
    ?>
</div>
