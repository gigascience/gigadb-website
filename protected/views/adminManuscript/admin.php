
<h1>Manage Manuscripts</h1>

<a href="/adminManuscript/create" class="btn">Create A New Manuscript</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'manuscript-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'filter'=>$model,
	'columns'=>array(
		'identifier',
		'pmid',
		array('name'=> 'doi_search', 'value'=>'$data->dataset->identifier'),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
