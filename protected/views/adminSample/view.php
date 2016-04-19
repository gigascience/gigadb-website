
<h1>View Sample #<?php echo $model->id; ?></h1>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= MyHtml::link('Manage Samples', array('admin')) ?>]
</div>
<? } ?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
            'id',
            'species_id',
            array(
                'name'=>'attributesList',
                'type'=>'raw',
                'value'=> $model->getAttributesList(),
            ),
	),
)); ?>
