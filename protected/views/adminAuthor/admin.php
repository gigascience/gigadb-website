
<p class="text-left">
<h1>Manage Authors</h1>
</p>

<?php
	$user = null;
	if ( isset(Yii::app()->session['attach_user']) ) {
		$user = User::model()->findByPk(Yii::app()->session['attach_user']) ;
	}
?>
<div class="clear"></div>
<?php if (null != $user ) { ?>
	<?php
		$existing_link = Author::findAttachedAuthorByUserId($user->id);
		if (null == $existing_link) {
	?>
			<div class="alert alert-info">
				<?php echo CHtml::link('&times;', array('adminAuthor/prepareUserLink',
                   'user_id'=>$user->id,'abort'=>'yes'), array('class'=>'close', 'data-dismiss'=>'alert')); ?>
				Click on a row to proceed with linking that author with user <? echo $user->first_name . " " . $user->last_name ?></div>
	<? } else { ?>
				<div class="alert alert-warning">
				<?php echo CHtml::link('&times;', array('adminAuthor/prepareUserLink',
                   'user_id'=>$user->id,'abort'=>'yes'), array('class'=>'close', 'data-dismiss'=>'alert')); ?>
					The user <? echo $user->first_name . " " . $user->last_name ?> is already associated to author <? echo $existing_link->getDisplayName()." (".$existing_link->id.")" ?>
					</div>
	<? } ?>
<? } ?>

<div class="row">
	<div class="span3">
		<a href="/adminAuthor/create" class="btn">Create a new author</a>
</div>

</div>
<div class="row">&nbsp;</div>

<div class="row">
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'author-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'selectionChanged'=>"function(id){open_controls($.fn.yiiGridView.getSelection(id));}",
	'filter'=>$model,
	'selectionChanged'=>"function(id){open_controls($.fn.yiiGridView.getSelection(id));}",
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

<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
  'id'=>'controls',
  // additional javascript options for the dialog plugin
  'options'=>array(
    'title'=>'Managing User',
    'autoOpen'=>false,
    'modal'=>true,
  ),
));

?>

<?php if (null != $user) { ?>
	<a href="#" class="btn btn-active" title="link" onclick="link_to_author();">Link user <? echo $user->first_name . " " . $user->last_name ?> to that author</a>
<div class="clear"></div>
<?php echo CHtml::link('Abort and clear selected user', array('adminAuthor/prepareUserLink',
                   'user_id'=>$user->id,'abort'=>'yes'), array('class'=>'btn btn-active')); ?>

<? } ?>


<?php
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<script>
	function open_controls(author_id) {
	    $("#controls").data('author_id', author_id);
	    $("#controls").dialog( "option", "title", "Linking to author id: " + author_id);
	    $("#controls").dialog("open");
	    return false;
	}

	function link_to_author() {
	<?
		echo 'var base_url = "'.Yii::app()->urlManager->createUrl('adminAuthor/linkUser',array('id'=>'')).'";'
	?>
		var author_id = $("#controls").data('author_id');
		window.location= base_url + "/" + author_id; 
	}

</script>
