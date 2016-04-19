
<h1>Manage Authors</h1>

<a href="/adminAuthor/create" class="btn">Create a new author</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'author-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'filter'=>$model,
	'columns'=>array(
		'surname',
		'middle_name',
		'first_name',
		'orcid',
		//'rank',
		array('name'=> 'dois_search', 'value'=>'$data->listOfDataset'),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
