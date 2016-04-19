
<h1>Manage External Links</h1>

<a href="/adminExternalLink/create" class="btn">Create New External Link</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'external-link-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'filter'=>$model,
	'columns'=>array(
		array('name'=> 'doi_search', 'value'=>'$data->dataset->identifier'),
		array('name'=> 'external_link_type_search', 'value'=>'$data->external_link_type->name'),
		'url',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
