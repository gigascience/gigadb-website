
<h2>Manage Rss Messages</h2>

<div class="clear"></div>
<a href="/rssMessage/create" class="btn">Create an RSS Message</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'rss-message-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'itemsCssClass'=>'table table-bordered',
	'columns'=>array(
		'id',
		'message',
		'publication_date',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
