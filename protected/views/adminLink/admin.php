<div class="container">

	<?php
	$this->widget('application.components.TitleBreadcrumb', [
		'pageTitle' => 'Manage Links',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
		]
	]);
	?>


	<div class="mb-10">
		<a href="/adminLink/create" class="btn background-btn">Create A New Link</a>

	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>


	<?php $this->widget('CustomGridView', array(
		'id' => 'link-grid',
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => array(
			array(
				'name' => 'doi_search', 'value' => '$data->dataset->identifier', 'headerHtmlOptions' => array('style' => 'width: 150px')
			),
			'is_primary',
			'link',
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>

</div>