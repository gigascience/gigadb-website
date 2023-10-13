<div class="container">
	<?php
	$this->widget('application.components.TitleBreadcrumb', [
		'pageTitle' => 'Manage Files',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
		]
	]);
	?>

	<div class="mb-10">
		<a href="/adminFile/create" class="btn background-btn">Create New File</a>&nbsp;&nbsp;<a href="/adminFile/linkFolder" class="btn background-btn">Link Temp File Folder</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>


	<?php $this->widget('CustomGridView', array(
		'id' => 'file-grid',
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => array(
			array('name' => 'doi_search', 'value' => '$data->dataset->identifier'),
			'code',
			array(
				'name' => 'name',
				'value' => '$data->name',
				'htmlOptions' => array('style' => 'width:20px;'),
			),
			//'location',
			//'extension',

			'date_stamp',
			array('name' => 'format_search', 'value' => '$data->format->name'),
			array('name' => 'type_search', 'value' => '$data->type->name'),
			array('name' => 'download_count', 'value' => '$data->download_count', 'headerHtmlOptions' => array('style' => 'width: 70px')),

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