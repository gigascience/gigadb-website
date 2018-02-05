
<p class="text-left">
<h1>Manage Authors</h1>
</p>

<div class="row">
<?php

	if ( isset(Yii::app()->session['attach_user']) && preg_match("/^\d+$/", Yii::app()->session['attach_user'] ) ) {
		$user = User::model()->findByPk(Yii::app()->session['attach_user']) ;
		if (null != $user) {
?>
			<div class="alert alert-block">
				<p>
					<?php echo "Click on a row to link that author with user " . $user->first_name . " " . $user->last_name ; ?>
				</p>
			</div>
<?php
		}
	}
?>
</div>
<div class="row">
	<div class="span3">
		<a href="/adminAuthor/create" class="btn">Create a new author</a>
	</div>
<?php

	if ( isset(Yii::app()->session['attach_user']) && preg_match("/^\d+$/", Yii::app()->session['attach_user'] ) ) {
?>
	<div class="span3">
		<a href="/adminAuthor/admin/attach_user/abort" class="btn btn-danger">Cancel attaching author</a>
	</div>
<?php
	}
?>

</div>
<div class="row">&nbsp;</div>

<div class="row">
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'author-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'filter'=>$model,
	'selectionChanged'=>"function(id){window.location='"
          .Yii::app()->urlManager->createUrl('adminAuthor/update',array('id'=>''))."/' + 
          $.fn.yiiGridView.getSelection(id);}",
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
</div>
