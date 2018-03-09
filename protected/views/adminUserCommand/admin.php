<h2>Managing Users Claims</h2>
<div class="clear"></div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'user-command-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'itemsCssClass'=>'table table-bordered',
	'columns'=>array(
		array(
            'name'=>'action_label',
            'value'=>'"<span title=\"Created on: ".$data->request_date."\">".$data->action_label."</span>"',
            'type'  => 'raw',
        ),
		array(
            'name'=>'requester_id',
            // 'value'=>'$data->requester->first_name." ".$data->requester->last_name." (".$data->requester->id.")"',
            'value'=>'CHtml::link($data->requester->first_name." ".$data->requester->last_name." (".$data->requester->id.")",array("User/view","id" => $data->requester_id ), array("data-original-title"=>"Click to view user info", "rel"=>"tooltip"))',
            'type'  => 'raw',
        ),
		array(
            'name'=>'actionable_id',
            // 'value'=>'Author::model()->findByPk($data->actionable_id)->getDisplayName()." (Author ".$data->actionable_id.")"',
            'value'=>'"claim_author" == $data->action_label ? CHtml::link(Author::model()->findByPk($data->actionable_id)->getDisplayName()." (Author ".$data->actionable_id.")",array("AdminAuthor/view","id" => $data->actionable_id ), array("data-original-title"=>"Click to view linked Author info", "rel"=>"tooltip")) : $data->actionable_id;',
            'type'  => 'raw',
        ),
		array(
            'name'=>'actioner_id',
            'value'=>'($data->action_date && $data->approved_by)?"<span title=\"Changed on: ".$data->action_date."\">".$data->approved_by->first_name." ".$data->approved_by->last_name." (".$data->approved_by->role.") </span>":""',
            'type'  => 'raw',
        ),
		'status',
		array(
			'class'=>'bootstrap.widgets.BootButtonColumn',
			'header'=>'Action',
			'template'=>'{validate}&nbsp;{invalidate}&nbsp;{delete}',
			'htmlOptions' => array('style' => 'white-space: nowrap'),
			'buttons'=>array
		    (
		        'validate' => array
		        (
		            'label'=>'Validate claim',
		            'icon'=>'icon-thumbs-up',
		            'url'=>'Yii::app()->createUrl("adminUserCommand/validate", array("id"=>$data->id))',
		            'deleteConfirmation' => null,
                    'options'=>array(
                        'class'=>'btn-mini',
                        'id'=> 'validate'
                    )
		        ),
		        'invalidate' => array
		        (
		            'label'=>'Reject claim',
		            'icon'=>'icon-thumbs-down',
		            'url'=>'Yii::app()->createUrl("adminUserCommand/reject", array("id"=>$data->id))',
		            'deleteConfirmation' => null,
		            'options'=>array(
                        'class'=>'btn-mini',
                        'id'=> 'reject'
                    )
		        ),
		    ),
		),
	),
)); ?>
