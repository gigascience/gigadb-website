<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Update Logs',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
		]
	]);
	?>

	<div class="mb-10">
		<a href="/datasetLog/create" class="btn background-btn">Add an update log</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>

	<?php $this->widget('CustomGridView', array(
		'id' => 'dataset-log-grid',
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => array(
			'id',
			'dataset_id',
			array('name' => 'doi', 'value' => '$data->dataset->identifier'),
			'message',
			'created_at',
			'model',
			'model_id',
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>

</div>