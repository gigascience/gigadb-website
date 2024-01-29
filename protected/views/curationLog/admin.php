
<h2>Manage Curation Log</h2>
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
