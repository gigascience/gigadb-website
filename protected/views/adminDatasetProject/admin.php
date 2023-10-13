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
			array(
				'class' => 'CButtonColumn',
				'header' => "Actions",
				'headerHtmlOptions' => array('style' => 'width: 120px'),
				'buttons' => array(
					'view' => array(
						'imageUrl' => false,
						'label' => '',
						'options' => array(
							"title" => "View Dataset",
							"class" => "fa fa-eye fa-lg icon icon-view",
							"aria-label" => "View Dataset"
						),
					),
					'update' => array(
						'imageUrl' => false,
						'label' => '',
						'options' => array(
							"title" => "Update Dataset",
							"class" => "fa fa-pencil fa-lg icon icon-update",
							"aria-label" => "Update Dataset"
						),
					),

					'delete' => array(
						'imageUrl' => false,
						'label' => '',
						'options' => array(
							"title" => "Delete Dataset",
							"class" => "fa fa-trash fa-lg icon icon-delete",
							"aria-label" => "Delete Dataset"
						),
					),
				),
			),
		),
	)); ?>

</div>