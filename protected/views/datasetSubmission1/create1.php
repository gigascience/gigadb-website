<h2>Create Dataset</h2>
<div class="clear"></div>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'dataset-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'),
        ));
?>

<a href="#" class="btn sw-selected-btn"><?= Yii::t('app' , 'Study')?></a>
<a href="/datasetSubmission1/authorManagement/id/<?= $model->id ?>" class="btn nomargin js-submit"><?= Yii::t('app' , 'Author')?></a>
<a href="/datasetSubmission1/projectManagement/id/<?= $model->id ?>" class="btn nomargin js-submit"><?= Yii::t('app' , 'Project')?></a>
<a href="/datasetSubmission1/linkManagement/id/<?= $model->id ?>" class="btn nomargin js-submit"><?= Yii::t('app' , 'Link')?></a>
<a href="/datasetSubmission1/exLinkManagement/id/<?= $model->id ?>" class="btn nomargin js-submit"><?= Yii::t('app' , 'External Link')?></a>
<a href="/datasetSubmission1/relatedDoiManagement/id/<?= $model->id ?>" class="btn nomargin js-submit"><?= Yii::t('app' , 'Related Doi')?></a>
<a href="/datasetSubmission1/sampleManagement/id/<?= $model->id ?>" class="btn nomargin js-submit"><?= Yii::t('app' , 'Sample')?></a>

<? 
    $this->renderPartial('_form1', array('model'=>$model, 'form'=>$form,'image'=>$image));     
?>



<?php $this->endWidget(); ?>

<script>
	$('.js-submit').click(function(e) {
		e.preventDefault();
		$('#dataset-form').submit();
	});
</script>


