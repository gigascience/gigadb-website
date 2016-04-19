<h1>Manage Dataset Funders</h1>
<a href="/datasetFunder/create" class="btn">Add New Dataset Funders</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'dataset-funder-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass' => 'table table-bordered',
	'filter'=>$model,
	'columns'=>array(		
		array(
			'name' => 'doi_search',
			'value' => '$data->dataset->identifier',
		),
		array(
			'name' => 'funder_search',
			'value' => '$data->funder->primary_name_display',
		),
		'grant_award',
		'comments',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
