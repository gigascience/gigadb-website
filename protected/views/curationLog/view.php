
<h1>View Curation Log #<?php echo $model->id; ?></h1>

<?php 

$dataset = Dataset::model()->find('id=:dataset_id', array(':dataset_id'=>$model->dataset_id));
 




$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		 array(
                    'name'=>'DataSet',
                    'value'=> $dataset->identifier,
                ),
		'creation_date',
                'created_by',
		'last_modified_date',
                'last_modified_by',
                'action',
                'comments',
	),
)); ?>


<?php echo CHtml::link('Back to this Dataset Curation Log', $this->createAbsoluteUrl('adminDataset/update',array('id'=>$model->dataset_id))); ?>
