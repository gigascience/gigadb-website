<?php

$dataset = Dataset::model()->find('id=:dataset_id', array(':dataset_id'=>$dataset_id));

?>

<h2>Dataset <?php echo $dataset->identifier?> Curation Log</h2>

<br>

<a href="/curationlog/create/id/<?php echo $dataset_id ?>" class="btn">Create New Log</a>

<div class="clear"></div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'dataset-grid',
	'dataProvider'=>$model,
	'itemsCssClass'=>"table table-bordered",
	'columns'=>array(             
		'creation_date',
		'created_by',
                'action',
		'comments',
		'last_modified_date',
		'last_modified_by',
		
		array(
			'class'=>'CButtonColumn',
                     'buttons'=>array(
                'view' => array(
                        'url' => 'Yii::app()->createUrl("curationlog/view" , array("id" => $data->id))'
                        )
                ),
		),
            
	),
)); ?>

