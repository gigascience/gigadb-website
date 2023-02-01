<?php
$dataset = Dataset::model()->find('id=:dataset_id', [':dataset_id' => $dataset_id]);

?>
<a href="/curationLog/create/id/<?php echo $dataset_id; ?>" class="btn">Create New Log</a>
<div class="clear"></div>

<?php
$this->widget(
    'zii.widgets.grid.CGridView',
    [
        'id'            => 'dataset-grid',
        'dataProvider'  => $model,
        'itemsCssClass' => 'table table-bordered',
        'columns'       => [
            'creation_date',
            'created_by',
            'action',
            'comments',
            'last_modified_date',
            'last_modified_by',
            [
                'class'   => 'CButtonColumn',
                'buttons' => [
                    'view'   => ['url' => 'Yii::app()->createUrl("curationLog/view", ["id" => $data->id])'],
                    'update' => ['url' => 'Yii::app()->createUrl("curationLog/update", ["id" => $data->id])'],
                    'delete' => ['url' => 'Yii::app()->createUrl("curationLog/delete", ["id" => $data->id])'],
                ],
            ],
        ],
    ]
);
