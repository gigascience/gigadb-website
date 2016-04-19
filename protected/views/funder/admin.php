
<h1>Manage Funders</h1>
<a href="/funder/create" class="btn">Add New Funder</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'funder-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass' =>'table table-bordered',
	'filter'=>$model,
	'columns'=>array(
		'id',
		'uri',
		'primary_name_display',
		'country',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
