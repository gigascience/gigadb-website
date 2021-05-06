<h2>Update Dataset <?php echo $model->identifier; ?></h2>
<div class="clear"></div>
<?php echo $this->renderPartial('_form', array('model'=>$model,'dataset_id'=>$dataset_id,'curationlog'=>$curationlog, 'datasetPageSettings' => $datasetPageSettings )); ?>
