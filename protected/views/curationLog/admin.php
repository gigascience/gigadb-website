<?php
$this->widget('TitleBreadcrumb', [
    'pageTitle' => 'Manage Curation Log',
    'breadcrumbItems' => [
        ['label' => 'Admin', 'href' => '/site/admin'],
        ['isActive' => true, 'label' => 'Manage'],
    ]
]);
?>
<br>
<div class="clear"></div>
<?php
$this->widget(
    'zii.widgets.grid.CGridView',
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
                'buttons' => [
                    'view'   => ['url' => 'Yii::app()->createUrl("curationlog/view", array("id" => $data->id))'],
                    'update' => ['url' => 'Yii::app()->createUrl("curationlog/update" , array("id" => $data->id))'],
                    'delete' => ['url' => 'Yii::app()->createUrl("curationlog/delete" , array("id" => $data->id))'],
                ],
            ],
        ],
    ]
);
