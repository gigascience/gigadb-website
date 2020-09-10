<?php
$this->breadcrumbs=array(
	'Datasets'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Dataset', 'url'=>array('index')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('dataset-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<?php if( Yii::app()->user->hasFlash('success') ) { ?>
<div class="alert alert-success flash-success modal-header">
	<?php echo Yii::app()->user->getFlash('success'); ?>

</div>

<?php } else if (Yii::app()->user->hasFlash('error')) { ?>
	<div class="alert alert-error flash-error">
		<?php echo Yii::app()->user->getFlash('error'); ?>
	</div>
<?php } ?>

<?php if( Yii::app()->session["filedrop_id_".Yii::app()->user->id]) {
	[$doi, $fid] = Yii::app()->session["filedrop_id_".Yii::app()->user->id];
?>
	<div class="button-panel panel" role="alert">
		<div class="panel-heading header alert-success">A new drop box has been created for the dataset <?php  echo $doi ?>.</div>
  	<div class="panel-body controls">
<?php

	echo CHtml::link('Customize instructions','#', array('class' => 'btn btn-primary', 'data-toggle' => "modal", 'data-target' => "#editInstructions"));

	echo CHtml::link('Send instructions by email',
		                array('adminDataset/sendInstructions', 'id'=>$doi, 'fid'=>$fid),
                        array('class' => 'btn btn-primary')
                    );
?>
	</div>
	</div>
<?php } ?>

<h1>Manage Datasets</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done. Date should be exactly in this format: <b>yyyy-mm-dd</b>
</p>

<a href="/adminDataset/create" class="btn">Create Dataset</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'dataset-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>"table table-bordered table-fixed",
	'rowCssClassExpression' => '"dataset-".$data["identifier"]',
	'filter'=>$model,
	'columns'=>array(
		'id',
		'identifier',
		'manuscript_id',
                'title',
		// 'publisher',
		// 'dataset_size',
		// 'ftp_site',
		// 'upload_status',
		// 'excelfile',
		// 'excelfile_md5',
		'publication_date',
		array('name'=> 'curator_id', 'value'=>'$data->getCuratorName()'),
                'modification_date',
                'upload_status',

		array(
			'class'=>'CButtonColumn',
			'template' => '{view}{update}{dropbox}{delete}',
            'buttons'=>array(
                'view' => array(
	                	'imageUrl'=>Yii::app()->request->baseUrl.'/images/view_new.png',
                        'url' => 'Yii::app()->createUrl("dataset/view" , array("id" => $data->identifier))'
                        ),
                'update' => array(
	                	'imageUrl'=>Yii::app()->request->baseUrl.'/images/update_new.png',
	                	'options'=>array("title"=>"Update Dataset"),
                        ),
                'delete' => array(
	                	'imageUrl'=>Yii::app()->request->baseUrl.'/images/delete_new.png',
                        ),
                'dropbox' => array(
	                	'imageUrl'=>Yii::app()->request->baseUrl.'/images/dropbox.png',
                		'url' => 'Yii::app()->createUrl("adminDataset/assignFTPBox" , array("id" => $data->identifier))',
                		'options'=>array('title'=>'New Dropbox for this dataset'),
                		'label' => 'New Dropbox for this dataset',
                		'visible' => '"AssigningFTPbox" === $data->upload_status'
                	)
                ),
		),
	),
)); ?>

<?php if( Yii::app()->session["filedrop_id_".Yii::app()->user->id]) { ?>

<div class="modal fade" id="editInstructions" tabindex="-1" role="dialog" aria-labelledby="customizeInstructions">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Write custom upload instructions</h4>
      </div>
      <div class="modal-body">
        <form id="instructionsForm">
        	<label for="instructions" class="control-label">Instructions</label>
        	<textarea id="instructions" name="instructions"
      class="form-control" rows="6" cols="120" tabindex="0">
			</textarea>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <a id="saveLink" href="#" class="btn btn-primary" alt="Save changes" >Save changes</a>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
document.addEventListener("DOMContentLoaded", function(event) {//after deferred scripts loaded

	// $('#editInstructions').on('hidden.bs.modal', function (e) {
	//   console.log("Hidden!");
	// });
	$('#editInstructions').on('shown.bs.modal', function (e) {
	  document.querySelector("#instructions").focus();
	});
	// document.querySelector("#editInstructions").addEventListener("shown.bs.modal", function (event) {
	// 	console.log("Shown!");
	// 	document.querySelector("#instructions").focus();
	// });
	document.querySelector("#saveLink").addEventListener("click", function(event) {
		event.preventDefault();
		<?php
			echo 'var doi = "'.$doi.'";';
			echo 'var fid = "'.$fid.'";';
		?>
		var myForm = document.getElementById('instructionsForm') ;
		myForm.method = 'post';
		myForm.action = "/adminDataset/saveInstructions/id/"+doi+"/fid/" +fid ;
		myForm.submit();
	});
});
</script>
<?php } ?>