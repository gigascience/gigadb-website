<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage External Links',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage External Links'],
		]
	]);
	?>

	<div class="mb-10">
		<a href="/adminExternalLink/create" class="btn background-btn">Create New External Link</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>

	<?php $this->widget('CustomGridView', array(
		'id' => 'external-link-grid',
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => array(
			array('name' => 'doi_search', 'value' => '$data->dataset->identifier', 'headerHtmlOptions' => array('style' => 'width: 120px')),
			array('name' => 'external_link_type_search', 'value' => '$data->external_link_type->name'),
			'url',
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>

</div>