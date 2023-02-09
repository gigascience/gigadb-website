<h1>Create Curation Log</h1>
<?php if (Yii::app()->user->checkAccess('admin') === true) { ?>
<div class="actionBar">
<?php CHtml::link('Manage Logs', ['admin']); ?>
</div>
<?php } ?>

<?php
echo $this->renderPartial(
    '_form1',
    [
        'model'      => $model,
        'dataset_id' => $dataset_id,
    ]
);

