<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Manuscripts',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
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
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>

</div>