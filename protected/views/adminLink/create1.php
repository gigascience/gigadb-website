
<h2>Cross reference data</h2>

<div class="clear"></div>


<a href="/dataset/create1" class="btn span1"><?= Yii::t('app' , 'Study')?></a>
<a href="/adminDatasetAuthor/create1" class="btn nomargin"><?= Yii::t('app' , 'Author')?></a>

<a href="/adminDatasetProject/create1" class="btn nomargin"><?= Yii::t('app' , 'Project')?></a>
<input type="submit" value="Link" class="btn-green-active  nomargin"></input>


<a href="/adminExternalLink/create1" class="btn nomargin"><?= Yii::t('app' , 'ExternalLink')?></a>
<a href="/adminRelation/create1" class="btn nomargin"><?= Yii::t('app' , 'Related Doi')?></a>
<a href="/adminDatasetSample/create1" class="btn nomargin"><?= Yii::t('app' , 'Sample')?></a>

<? if(isset($_SESSION['filecount']) && $_SESSION['filecount']>0) {?>
<a href="/adminFile/create1" class="btn nomargin"><?= Yii::t('app' , 'File')?></a>
<? } ?>

<?php echo $this->renderPartial('_form1', array('model'=>$model,'link_model'=>$link_model,
    'link_database'=>$link_database)); ?>
