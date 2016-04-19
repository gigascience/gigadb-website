
<h1>Manage Images</h1>
<a href="/adminImages/create" class="btn">Create New Image</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'images-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'filter'=>$model,
	'columns'=>array(
		'id',
		'location',
		'tag',
		'license',
		'photographer',
		/*
		'source',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
