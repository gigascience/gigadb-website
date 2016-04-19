<h1>Manage Dataset - Samples</h1>

<a href="/adminDatasetSample/create" class="btn">Add a Sample to a Dataset</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'dataset-sample-grid',
	'ajaxUpdate' => false,
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'filter'=>$model,
	'columns'=>array(
		array('name'=> 'doi_search', 'value'=>'$data->dataset->identifier'),
		'sample_id',
		array('name'=> 'sample_name', 'value'=>'$data->sample->name'),
		array('header'=> 'Sample Attributes','type'=>'raw' ,'value'=>'$data->sample->displayAttr'),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>

<?php
$clientScript = Yii::app()->clientScript;
$register_script = <<<EO_SCRIPT
    jQuery(".js-desc").click(function(e) {
        e.preventDefault();
        id = $(this).attr('data');
        jQuery(this).hide();
        jQuery('.js-short-'+id).toggle();
        jQuery('.js-long-'+id).toggle();
    })
EO_SCRIPT;
$clientScript->registerScript('register_script', $register_script, CClientScript::POS_READY);
?>
