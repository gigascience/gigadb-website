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

<div class="container">
	<?php
    $this->widget('TitleBreadcrumb', [
        'pageTitle' => 'Manage Datasets',
        'breadcrumbItems' => [
						['label' => 'Admin', 'href' => '/site/admin'],
            ['isActive' => true, 'label' => 'Manage'],
        ]
    ]);
    ?>


<div class="mb-10">
  <a href="/adminDataset/create" class="btn background-btn">Create Dataset</a>
  </div>

  <div class="panel panel-success">
      <!-- NOTE: remove inline styles once https://github.com/gigascience/gigadb-website/pull/1701 is merged -->
      <div class="panel-heading" style="padding: .5rem;">
          <h4 class="panel-title">Table Instructions</h4>
      </div>
      <div class="panel-body">
          <p>To filter the dataset listings, you can prepend your search terms in the fields below with comparison operators:</p>
          <ul style="color: gray;">
              <li><strong>&lt;</strong> (less than)</li>
              <li><strong>&lt;=</strong> (less than or equal to)</li>
              <li><strong>&gt;</strong> (greater than)</li>
              <li><strong>&gt;=</strong> (greater than or equal to)</li>
              <li><strong>&lt;&gt;</strong> (not equal to)</li>
              <li><strong>=</strong> (equal to)</li>
          </ul>
          <p>For date fields, ensure the format is strictly <code>yyyy-mm-dd</code>.</p>
          <p>
            Column headers with links are sortable. Cells with a text input are used for filtering.
          </p>
      </div>
  </div>

	<?php $this->widget('CustomGridView', array(
		'id'=>'dataset-grid',
    'afterAjaxUpdate' => 'afterAjaxUpdate',
		'dataProvider'=>$dataProvider,
		'itemsCssClass'=>"table table-bordered table-fixed dataset-table",
		'rowCssClassExpression' => '"dataset-".$data["identifier"]',
		'filter'=>$model,
		'columns'=>array(
			'id',
			'identifier',
			'manuscript_id',
      array(
        'name' => 'title',
        'type' => 'raw',
        'value' => 'Yii::app()->controller->widget("CHtmlPurifier")->purify($data->title)',
        ),
			// 'publisher',
			// 'dataset_size',
			// 'ftp_site',
			// 'upload_status',
			// 'excelfile',
			// 'excelfile_md5',
			'publication_date',
			// array('name'=> 'curator_id', 'value'=>'$data->getCuratorName()'),
			'modification_date',
			array(
				'class'=>'CDataColumn',
				'header' => "Upload Status",
				'headerHtmlOptions'=>array('style'=>'width: 150px'),
				'value'  => '$data->upload_status'
			),
			array(
				'class'=>'CButtonColumn',
				'header' => "Actions",
				'headerHtmlOptions'=>array('style'=>'width: 120px'),
				'template' => '{view}{update}{dropbox}{delete}',
				'buttons' => array(
					'view' => array(
						'imageUrl' => false,
						'url' => 'Yii::app()->createUrl("dataset/view" , array("id" => $data->identifier))',
						'label' => '',
						'options' => array(
							"title" => "View Dataset",
							"class" => "fa fa-eye fa-lg icon icon-view",
							"aria-label" => "View Dataset"
						),
					),
					'update' => array(
						'imageUrl' => false,
						'label' => '',
						'options' => array(
							"title" => "Update Dataset",
							"class" => "fa fa-pencil fa-lg icon icon-update",
							"aria-label" => "Update Dataset"
						),
					),

					'delete' => array(
						'imageUrl' => false,
						'label' => '',
						'options' => array(
							"title" => "Delete Dataset",
							"class" => "fa fa-trash fa-lg icon icon-delete",
							"aria-label" => "Delete Dataset"
						),
					),

					'dropbox' => array(
						'imageUrl' => false,
						'url' => 'Yii::app()->createUrl("adminDataset/assignFTPBox" , array("id" => $data->identifier))',
						'label' => '',
						'visible' => '"AssigningFTPbox" === $data->upload_status',
						'options' => array(
							'title' => 'New Dropbox for this dataset',
							"class" => "fa fa-inbox fa-lg icon icon-dropbox",
							"aria-label" => "New Dropbox for this dataset"
						),
					)

				),
			),
		),
	)); ?>

</div>

<?php if( Yii::app()->session["filedrop_id_".Yii::app()->user->id]) { ?>

<div class="modal fade" id="editInstructions" tabindex="-1" role="dialog" aria-labelledby="customizeInstructions">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Customise upload instructions</h4>
      </div>
      <div class="modal-body">
        <form id="instructionsForm">
        	<label for="instructions" class="control-label">Instructions</label>
        	<textarea id="instructions" name="instructions"
      class="form-control" rows="6" cols="120" tabindex="0">
			</textarea>
        </form>
        <div class="panel panel-success" style="margin:1em;width:100%">
                        <div class="panel-heading">
                            <h4 class="panel-title">Tips</h4>
                        </div>
                        <div class="panel-body">
                        	Clicking the "Save changes" button won't send the email yet.
                        	You will still need to click on "Send instructions by email" to do so.
                        </div>
        </div>
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