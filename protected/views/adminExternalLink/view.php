<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'View ExternalLink #' . $model->id,
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/adminExternalLink/admin'],
			['isActive' => true, 'label' => 'View'],
		]
	]);

	$this->widget('zii.widgets.CDetailView', array(
		'data' => $model,
		'attributes' => array(
			'id',
			'dataset_id',
			'url',
			'external_link_type_id',
		),
		'htmlOptions' => array('class' => 'table table-striped table-bordered dataset-view-table'),
		'itemCssClass' => array('odd', 'even'),
		'itemTemplate' => '<tr class="{class}"><th scope="row">{label}</th><td>{value}</td></tr>'
	)); ?>

</div>