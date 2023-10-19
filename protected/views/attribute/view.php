<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'View Attribute #' . $model->id,
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/attribute/admin'],
			['isActive' => true, 'label' => 'View'],
		]
	]);
	$this->widget('zii.widgets.CDetailView', array(
		'data' => $model,
		'attributes' => array(
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
		'htmlOptions' => array('class' => 'table table-striped table-bordered dataset-view-table'),
		'itemCssClass' => array('odd', 'even'),
		'itemTemplate' => '<tr class="{class}"><th scope="row">{label}</th><td>{value}</td></tr>'

	)); ?>

</div>