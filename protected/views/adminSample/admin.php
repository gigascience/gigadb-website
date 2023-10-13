<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Files',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage Samples'],
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
			array(
				'class' => 'CButtonColumn',
				'header' => "Actions",
				'headerHtmlOptions' => array('style' => 'width: 100px'),
				'template' => '{view}{update}{delete}',
				'buttons' => array(
					'view' => array(
						'imageUrl' => false,
						'label' => '',
						'options' => array(
							"title" => "View",
							"class" => "fa fa-eye fa-lg icon icon-view",
							"aria-label" => "View"
						),
					),
					'update' => array(
						'imageUrl' => false,
						'label' => '',
						'options' => array(
							"title" => "Update",
							"class" => "fa fa-pencil fa-lg icon icon-update",
							"aria-label" => "Update"
						),
					),
					'delete' => array(
						'imageUrl' => false,
						'label' => '',
						'options' => array(
							"title" => "Delete",
							"class" => "fa fa-trash fa-lg icon icon-delete",
							"aria-label" => "Delete"
						),
					),
				),
			),
		),
	)); ?>

</div>