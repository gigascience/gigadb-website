<div class="container">
	<?php
		$this->widget('application.components.TitleBreadcrumb', [
			'pageTitle' => 'Manage Dataset - Authors',
			'breadcrumbItems' => [
				['label' => 'Admin', 'href' => '/site/admin'],
					['isActive' => true, 'label' => 'Dataset:Authors'],
			]
	]);
	?>
	<a href="/adminDatasetAuthor/create" class="btn background-btn">Add an author to a Dataset</a>

	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'dataset-author-grid',
		'dataProvider'=>$model->search(),
		'itemsCssClass'=>'table table-bordered',
		'filter'=>$model,
		'columns'=>array(
			array('name'=> 'doi_search', 'value'=>'$data->dataset->identifier' , 'sortable' => True, 'htmlOptions'=>array('width'=>'200')),
			array('name'=> 'author_name_search', 'value'=>'$data->author->name'),
			array('name'=> 'orcid_search', 'value'=>'$data->author->orcid', 'htmlOptions'=>array('width'=>'200')),
			array('name'=> 'rank_search', 'value'=>'$data->rank', 'htmlOptions'=>array('width'=>'100')),
			array(
				'header'=>'Options',
				'class'=>'CButtonColumn',
				'htmlOptions'=>array('width'=>'75')
			),
		),
	)); ?>

</div>