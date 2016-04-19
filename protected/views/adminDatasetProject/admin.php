<h1>Manage Dataset - Projects</h1>

<a href="/adminDatasetProject/create" class="btn">Add a Project to a Dataset</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'dataset-project-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'filter'=>$model,
	'columns'=>array(
		array('name'=> 'doi_search', 'value'=>'$data->dataset->identifier'),
		array('name'=> 'project_name_search', 'value'=>'$data->project->name'),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
