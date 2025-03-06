<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'View Project #' . $model->id,
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/adminProject/admin'],
			['isActive' => true, 'label' => 'View'],
		]
	]);
	$this->widget('zii.widgets.CDetailView', [
		'data' => $model,
		'attributes' => [
			'id',
			'url',
			'name',
			[
				'name' => 'image_location',
				'label' => 'Image',
				'type' => 'raw',
				'value' => !empty($model->image_location) ?
					CHtml::image($model->image_location, CHtml::encode($model->name), ['style' => 'max-width: auto; max-height: 60px;']) :
					'<span>(not set)</span>'
			],
		],
		'htmlOptions' => ['class' => 'table table-striped table-bordered dataset-view-table'],
		'itemCssClass' => ['odd', 'even'],
		'itemTemplate' => '<tr class="{class}"><th scope="row">{label}</th><td>{value}</td></tr>'
	]);
	?>

</div>