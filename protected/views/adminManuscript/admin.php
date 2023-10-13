<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Files',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage Manuscripts'],
		]
	]);
	?>

	<div class="mb-10">
		<a href="/adminManuscript/create" class="btn background-btn">Create A New Manuscript</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>

	<?php $this->widget('CustomGridView', array(
		'id' => 'manuscript-grid',
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => array(
			'identifier',
			'pmid',
			array('name' => 'doi_search', 'value' => '$data->dataset->identifier', 'headerHtmlOptions' => array('style' => 'width: 120px')),
			array(
				'class' => 'CButtonColumn',
				'header' => "Actions",
				'headerHtmlOptions' => array('style' => 'width: 120px'),
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