<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Dataset - Authors',
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Dataset:Authors'],
		]
	]);
	?>
	<a href="/adminDatasetAuthor/create" class="btn background-btn">Add an author to a Dataset</a>

	<div class="sr-only">
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</div>

	<?php $this->widget('CustomGridView', array(
		'id' => 'dataset-author-grid',
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => array(
			array('name' => 'doi_search', 'value' => '$data->dataset->identifier', 'sortable' => True, 'htmlOptions' => array('width' => '200')),
			array('name' => 'author_name_search', 'value' => '$data->author->name'),
			array('name' => 'orcid_search', 'value' => '$data->author->orcid', 'htmlOptions' => array('width' => '200')),
			array('name' => 'rank_search', 'value' => '$data->rank', 'htmlOptions' => array('width' => '100')),
			array(
				'header' => 'Actions',
				'class' => 'CButtonColumn',
				'htmlOptions' => array('width' => '75')
			),
		),
	)); ?>

</div>