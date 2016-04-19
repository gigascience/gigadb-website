<h2>Manage File Formats</h2>
<div class="clear"></div>
<p>
To list certain news items that you are looking for, you may search via keyword or value. Type your keyword or value into their respective boxes under the column headers and press the enter key. You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>
<a href="/adminFileFormat/create" class="btn">Create A New File Format</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'file-format-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'itemsCssClass'=>'table table-bordered',
	'columns'=>array(
		'name',
		'description',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
