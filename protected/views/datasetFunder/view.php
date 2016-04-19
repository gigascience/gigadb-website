
<a href="/datasetFunder/admin">Back</a>

<h1>View Dataset Funder #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(
			'label' => 'Dataset',
			'value' => $model->dataset->identifier,
		),
		array(
			'label' => 'Funder',
			'value' => $model->funder->primary_name_display,
		),
		'grant_award',
		'comments',
	),
)); ?>
