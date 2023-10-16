<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Dataset Funders',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
		]
	]);
	?>
	<div class="mb-10">
		<a href="/datasetFunder/create" class="btn background-btn">Add New Dataset Funders</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>

	<?php $this->widget('CustomGridView', array(
		'id' => 'dataset-funder-grid',
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => array(
			array(
				'name' => 'doi_search',
				'value' => '$data->dataset->identifier',
				'headerHtmlOptions' => array('style' => 'width: 120px')
			),
			array(
				'name' => 'funder_search',
				'value' => '$data->funder->primary_name_display',
			),
			'grant_award',
			'awardee',
			'comments',
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>

</div>