
<h1>Manage Links</h1>

<a href="/adminLink/create" class="btn">Create A New Link</a>


<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'link-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'filter'=>$model,
	'columns'=>array(
		array('name'=> 'doi_search', 'value'=>'$data->dataset->identifier'),
		'is_primary',
		'link',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
