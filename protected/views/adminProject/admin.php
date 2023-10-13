<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Files',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage Projects'],
		]
	]);
	?>
	<div class="mb-10">
		<a href="/adminProject/create" class="btn background-btn">Create New Project</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>

	<?php $this->widget('CustomGridView', array(
		'id' => 'project-grid',
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => array(
			'url',
			'name',
			'image_location',
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>

</div>