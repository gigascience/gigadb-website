<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'View Dataset Funder #' . $model->id,
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/datasetFunder/admin'],
			['isActive' => true, 'label' => 'View'],
		]
	]);
	?>

	<?php $this->widget('zii.widgets.CDetailView', array(
		'data' => $model,
		'attributes' => array(
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
			'awardee',
			'comments',
		),
		'htmlOptions' => array('class' => 'table table-striped table-bordered dataset-view-table'),
		'itemCssClass' => array('odd', 'even'),
		'itemTemplate' => '<tr class="{class}"><th scope="row">{label}</th><td>{value}</td></tr>'
	)); ?>
</div>