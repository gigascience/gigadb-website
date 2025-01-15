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
                [
                    'class'   => 'CButtonColumn',
                    'header' => "Actions",
                    'headerHtmlOptions' => array('style' => 'width: 100px'),
                    'template' => '{view}{update}{delete}',
                    'buttons' => array(
                        'view' => array(
                            'imageUrl' => false,
                            'url' => 'Yii::app()->createUrl("curationLog/view", ["id" => $data->id])',
                            'label' => '',
                            'options' => array(
                                "title" => "View",
                                "class" => "fa fa-eye fa-lg icon icon-view",
                                "aria-label" => "View"
                            ),
                        ),
                        'update' => array(
                            'imageUrl' => false,
                            'url' => 'Yii::app()->createUrl("curationLog/update", ["id" => $data->id])',
                            'label' => '',
                            'options' => array(
                                "title" => "Update",
                                "class" => "fa fa-pencil fa-lg icon icon-update",
                                "aria-label" => "Update"
                            ),
                        ),
                        'delete' => array(
                            'imageUrl' => false,
                            'url' => 'Yii::app()->createUrl("curationLog/delete", ["id" => $data->id])',
                            'label' => '',
                            'options' => array(
                                "title" => "Delete",
                                "class" => "fa fa-trash fa-lg icon icon-delete",
                                "aria-label" => "Delete"
                            ),
                        ),
                    ),
                ],
            ],
        ]
    );
    ?>
</div>
