
<h1>Manage Projects</h1>

<a href="/adminProject/create" class="btn">Create New Project</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'project-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'filter'=>$model,
	'columns'=>array(
		'url',
		'name',
		'image_location',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
