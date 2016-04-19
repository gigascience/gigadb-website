<?php
$this->breadcrumbs=array(
	'Dataset Projects'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List DatasetProject', 'url'=>array('index')),
	array('label'=>'Manage DatasetProject', 'url'=>array('admin')),
);
?>

<h2>Part of a project</h2>
<div class="clear"></div>

<a href="/dataset/create1" class="btn span1"><?= Yii::t('app' , 'Study')?></a>
<a href="/adminDatasetAuthor/create1" class="btn nomargin"><?= Yii::t('app' , 'Author')?></a>
<input type="submit" value="Project" class="btn-green-active  nomargin"></input>

<a href="/adminLink/create1" class="btn nomargin"><?= Yii::t('app' , 'Link')?></a>
<a href="/adminExternalLink/create1" class="btn nomargin"><?= Yii::t('app' , 'ExternalLink')?></a>
<a href="/adminRelation/create1" class="btn nomargin"><?= Yii::t('app' , 'Related Doi')?></a>
<a href="/adminDatasetSample/create1" class="btn nomargin"><?= Yii::t('app' , 'Sample')?></a>
<? if(isset($_SESSION['filecount']) && $_SESSION['filecount']>0) {?>
<a href="/adminFile/create1" class="btn nomargin"><?= Yii::t('app' , 'File')?></a>
<? } ?>

<?php echo $this->renderPartial('_form1', array('model'=>$model,'project_model'=>$project_model)); ?>