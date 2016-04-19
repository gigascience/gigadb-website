
<h2>Add Relationship with other DOI?</h2>
<div class="clear"></div>

<a href="/dataset/create1" class="btn span1"><?= Yii::t('app' , 'Study')?></a>

<a href="/adminDatasetAuthor/create1" class="btn nomargin"><?= Yii::t('app' , 'Author')?></a>

<a href="/adminDatasetProject/create1" class="btn nomargin"><?= Yii::t('app' , 'Project')?></a>
<a href="/adminLink/create1" class="btn nomargin"><?= Yii::t('app' , 'Link')?></a>

<a href="/adminExternalLink/create1" class="btn nomargin"><?= Yii::t('app' , 'ExternalLink')?></a>

<input  type="submit" class="btn-green-active nomargin" value="Related Doi"></input>

<a href="/adminDatasetSample/create1" class="btn nomargin"><?= Yii::t('app' , 'Samples')?></a>
<? if(isset($_SESSION['filecount']) && $_SESSION['filecount']>0) {?>
<a href="/adminFile/create1" class="btn nomargin"><?= Yii::t('app' , 'File')?></a>
<? } ?>

<?php echo $this->renderPartial('_form1', array('model'=>$model,'relation_model'=>$relation_model,
        'relation_type'=>$relation_type)); ?>
