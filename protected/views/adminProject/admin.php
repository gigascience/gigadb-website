<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Projects',
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
		]
	]);
	?>
	<div class="mb-10">
		<a href="/adminProject/create" class="btn background-btn">Create New Project</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>

	<?php $this->widget('CustomGridView', [
		'id' => 'project-grid',
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => [
			'url',
			'name',
			[
				'name' => 'image_location',
				'header' => 'Image',
				'type' => 'raw',
				'sortable' => false,
				'value' => '!empty($data->image_location) ?
					CHtml::image($data->image_location, CHtml::encode($data->name), ["style" => "max-width: auto; max-height: 60px;"]) :
					"<span>(not set)</span>"'
			],
			CustomGridView::getDefaultActionButtonsConfig()
		],
	]); ?>

</div>