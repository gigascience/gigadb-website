<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'View FileType #' . $model->id,
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/adminFileType/admin'],
			['isActive' => true, 'label' => 'View'],
		]
	]);
	$this->widget('zii.widgets.CDetailView', array(
		'data' => $model,
		'attributes' => array(
			'id',
			'name',
			'description',
		),
		'htmlOptions' => array('class' => 'table table-striped table-bordered dataset-view-table'),
		'itemCssClass' => array('odd', 'even'),
		'itemTemplate' => '<tr class="{class}"><th scope="row">{label}</th><td>{value}</td></tr>'
	)); ?>

</div>