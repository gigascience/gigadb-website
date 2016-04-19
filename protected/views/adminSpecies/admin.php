
<h1>Manage Species</h1>
<a href="/adminSpecies/create" class="btn">Create New Species</a>


<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'species-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'filter'=>$model,
	'columns'=>array(
		'tax_id',
		'common_name',
		'genbank_name',
		'scientific_name',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
