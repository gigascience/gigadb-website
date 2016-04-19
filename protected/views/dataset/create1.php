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
<a href="/dataset/authorManagement/id/<?= $model->id ?>" class="btn nomargin js-submit"><?= Yii::t('app' , 'Author')?></a>
<a href="/dataset/projectManagement/id/<?= $model->id ?>" class="btn nomargin js-submit"><?= Yii::t('app' , 'Project')?></a>
<a href="/dataset/linkManagement/id/<?= $model->id ?>" class="btn nomargin js-submit"><?= Yii::t('app' , 'Link')?></a>
<a href="/dataset/exLinkManagement/id/<?= $model->id ?>" class="btn nomargin js-submit"><?= Yii::t('app' , 'External Link')?></a>
<a href="/dataset/relatedDoiManagement/id/<?= $model->id ?>" class="btn nomargin js-submit"><?= Yii::t('app' , 'Related Doi')?></a>
<a href="/dataset/sampleManagement/id/<?= $model->id ?>" class="btn nomargin js-submit"><?= Yii::t('app' , 'Sample')?></a>

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


