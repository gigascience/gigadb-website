<a href="/attribute/admin">Back</a>
<h1>View Attribute #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
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
	),
)); ?>
