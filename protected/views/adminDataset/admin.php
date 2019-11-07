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
<div class="flash-success">
	<?php echo Yii::app()->user->getFlash('success'); ?>

</div>

<?php } else if (Yii::app()->user->hasFlash('error')) { ?>
	<div class="flash-error">
		<?php echo Yii::app()->user->getFlash('error'); ?>
	</div>
<?php } ?>

<?php if( Yii::app()->session["filedrop_id_".Yii::app()->user->id] ) {
	[$doi, $fid] = Yii::app()->session["filedrop_id_".Yii::app()->user->id];
	echo CHtml::link('Send instructions by email',
		                array('adminDataset/sendInstructions', 'id'=>$doi, 'fid'=>$fid),
                        array('class' => 'btn')
                    );
}
?>

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
