<h1>Manage Samples</h1>

<a href="/adminSample/create" class="btn">Create New Sample</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'sample-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'filter'=>$model,
	'columns'=>array(
		array('name'=>'name', 'value'=>'$data->name'),
		array('name'=> 'species_search', 'value'=>'$data->species->common_name'),
		array('name'=> 'dois_search', 'value'=>'$data->listOfDataset'),
		array('name'=> 'attr_search','type'=>'raw' ,'value'=>'$data->fullAttrDesc'),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>

