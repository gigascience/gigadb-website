<?php
$dataset = Dataset::model()->find('id=:dataset_id', [':dataset_id' => $dataset_id]);

?>
<a href="/curationLog/create/id/<?php echo $dataset_id; ?>" class="btn background-btn-o">Create New Log</a>
<div class="clear"></div>

<?php
$this->widget(
    'CustomGridView',
    [
        'id'            => 'dataset-grid',
        'dataProvider'  => $model,
        'itemsCssClass' => 'table table-bordered',
        'enableSorting'  => false,
        'columns'       => [
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
