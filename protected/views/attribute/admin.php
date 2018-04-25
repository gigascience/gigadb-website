
<h1>Manage Attribute</h1>
<a href="/attribute/create" class="btn">Add New Attribute</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'attribute-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass' =>'table table-bordered',
	'filter'=>$model,
	'columns'=>array(
		'id',
		'attribute_name',
		'definition',
		'model',
                'structured_comment_name',
                'value_syntax',
                'allowed_units',
                'occurance',
                'ontology_link',
                'note',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
