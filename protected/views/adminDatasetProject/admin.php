<div class="container">
	<?php
	$this->widget('application.components.TitleBreadcrumb', [
		'pageTitle' => 'Manage Dataset - Projects',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
		]
	]);
	?>

	<div class="mb-10">
		<a href="/adminDatasetProject/create" class="btn background-btn">Add a Project to a Dataset</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>

	<?php $this->widget('CustomGridView', array(
		'id' => 'dataset-project-grid',
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => array(
			array('name' => 'doi_search', 'value' => '$data->dataset->identifier', 'headerHtmlOptions' => array('style' => 'width: 120px')),
			array('name' => 'project_name_search', 'value' => '$data->project->name'),
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>

</div>