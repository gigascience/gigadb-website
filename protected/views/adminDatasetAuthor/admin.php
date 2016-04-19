<h1>Manage Dataset - Authors</h1>

<a href="/adminDatasetAuthor/create" class="btn">Add an author to a Dataset</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'dataset-author-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'filter'=>$model,
	'columns'=>array(
		array('name'=> 'doi_search', 'value'=>'$data->dataset->identifier' , 'sortable' => True ),
		array('name'=> 'author_name_search', 'value'=>'$data->author->name'),
		array('name'=> 'orcid_search', 'value'=>'$data->author->orcid'),
		array('name'=> 'rank_search', 'value'=>'$data->rank'),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
