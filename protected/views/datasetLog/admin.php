<h1>Manage Update Logs</h1>

<a href="/datasetLog/create" class="btn">Add an update log</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'dataset-log-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'filter'=>$model,
	'columns'=>array(
		'id',
		'dataset_id',
		array('name'=> 'doi', 'value'=>'$data->dataset->identifier'),
		'message',
		'created_at',
		'model',
		'model_id',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
