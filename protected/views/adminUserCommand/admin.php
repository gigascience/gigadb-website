<h2>Manage User Commands</h2>
<div class="clear"></div>

<a href="/adminUserCommand/create" class="btn">Create A New User Command</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'file-format-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'itemsCssClass'=>'table table-bordered',
	'columns'=>array(
		'action_label',
		array(
            'name'=>'requester_id',
            'value'=>'$data->requester->first_name." ".$data->requester->last_name." (".$data->requester->id.")"',
        ),
		'actioner_id',
		array(
            'name'=>'actionable_id',
            'value'=>'Author::model()->findByPk($data->actionable_id)->getDisplayName()." (".$data->actionable_id.")"',
        ),
		'request_date',
		'action_date',
		'status',
		array(
			'class'=>'CButtonColumn',
			'template'=>'{invalidate}{confirm}',
			'buttons'=>array
		    (
		        'confirm' => array
		        (
		            'label'=>'Validate claim',
		            'url'=>'Yii::app()->createUrl("adminUserCommand/validate", array("id"=>$data->id))'
		        ),
		        'invalidate' => array
		        (
		            'label'=>'Reject claim',
		            'url'=>'Yii::app()->createUrl("adminUserCommand/reject", array("id"=>$data->id))'
		        ),
		    ),
		),
	),
)); ?>
