
<h2>Add Authors</h2>
<div class="clear"></div>


<a href="/dataset/create1" class="btn span1"><?= Yii::t('app' , 'Study')?></a>
<input type="submit" id="author-btn" class="btn-green-active nomargin" value="Author"></input>
<a href="/adminDatasetProject/create1" class="btn nomargin"><?= Yii::t('app' , 'Project')?></a>
<a href="/adminLink/create1" class="btn nomargin"><?= Yii::t('app' , 'Link')?></a>
<a href="/adminExternalLink/create1" class="btn nomargin"><?= Yii::t('app' , 'ExternalLink')?></a>
<a href="/adminRelation/create1" class="btn nomargin"><?= Yii::t('app' , 'Related Doi')?></a>
<a href="/adminDatasetSample/create1" class="btn nomargin"><?= Yii::t('app' , 'Sample')?></a>

<? if(isset($_SESSION['filecount']) && $_SESSION['filecount'] > 0) {?>
<a href="/adminFile/create1" class="btn nomargin"><?= Yii::t('app' , 'File')?></a>
<? } ?>


<?php echo $this->renderPartial('_form1', array('model'=>$model,'author_model'=>$author_model)); ?>