<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Samples',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
		]
	]);
	?>

	<div class="mb-10">
		<a href="/adminSample/create" class="btn background-btn">Create New Sample</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>

	<?php $this->widget('CustomGridView', array(
		'id' => 'sample-grid',
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => array(
			array('name' => 'name', 'value' => '$data->name'),
			array('name' => 'species_search', 'value' => '$data->species->common_name'),
			array('name' => 'dois_search', 'value' => '$data->listOfDataset', 'headerHtmlOptions' => array('style' => 'width: 120px')),
			array('name' => 'attr_search', 'type' => 'raw', 'value' => 'FormattedDatasetSamples::fullAttrDesc($data->getSampleAttributeArrayMap())'),
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>

</div>