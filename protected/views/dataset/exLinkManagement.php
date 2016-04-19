<h2>Add link to a genome browser or website?</h2>
<div class="clear"></div>

<a href="/dataset/datasetManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Study')?></a>
<a href="/dataset/authorManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Author')?></a>
<a href="/dataset/projectManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Project')?></a>
<a href="/dataset/linkManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Link')?></a>
<a href="#" class="btn sw-selected-btn"><?= Yii::t('app' , 'External Link')?></a>
<a href="/dataset/relatedDoiManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Related Doi')?></a>
<a href="/dataset/sampleManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Sample')?></a>
<? if($model->isProteomic) { ?>
<a href="/dataset/pxInfoManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'PX Info')?></a>
<? } ?>
<? if($model->files && count($model->files) > 0) { ?>
<a href="/adminFile/create1/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'File')?></a>
<? } ?>

<div class="span12 form well">
    <div class="form-horizontal">
	<div id="author-grid" class="grid-view">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th id="author-grid_c0" width="45%">Url</th>
					<th id="author-grid_c0" width="45%">External Link Type</th>
					<th id="author-grid_c5" class="button-column" width="10%"></th>
				</tr>
			</thead>
			<tbody>
                   <?php if($exLinks) { ?>
				<?php foreach($exLinks as $exLink) { ?>
				<tr class="odd">
					<td><?= $exLink->url ?></td>
					<td><?= $exLink->externalLinkType->name ?></td>
					<td class="button-column">
						<a class="js-delete-exLink delete-title" exLink-id="<?=$exLink->id?>"  title="delete this row">
							<img alt="delete this row" src="/images/delete.png">
						</a>
					</td>
				</tr>
				<? } ?>
                  <? } else  { ?>
                  <tr>
                    <td colspan="4">
                      <span class="empty">No results found.</span>
                    </td>
                  </tr>
                  <tr>
                  <? } ?>
				</tbody>
		</table>
        </div>

          <p class="note">If your data is a genomic assembly that 
                is represented in a public genome browser, please add the direct URL here.</p>

            <div class="control-group">
                <label class='control-label'>External Link Type</label>
                <div class="controls">
                    <?= CHtml::dropDownList('exLink', null, CHtml::listData(ExternalLinkType::model()->findAll(), 'id', 'name'),array('class'=>'js-ex-link-type','style'=>'width:250px')); ?>
                </div>
            </div>

            <div class="control-group">
                <label class='control-label'>Url</label>
                <div class="controls">
                <?= CHtml::textField('exLink', '', array('class'=>'js-ex-link-url', 'size' => 60, 'maxlength' => 100, 'style'=>'width:240px', 'placeholder'=>'URL')); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="span12" style="text-align:center">
                    <a href="#" dataset-id="<?=$model->id?>" class="btn js-add-exLink"/>Add External Link</a>
                </div>
            </div>

    </div>

     <div class="span12" style="text-align:center">
        <a href="/dataset/linkManagement/id/<?= $model->id ?>" class="btn-green">Previous</a>
        <a href="/user/view_profile" title="Save your incomplete submission and leave the submission wizard." class="btn-green delete-title">Save & Quit</a>
        <a href="/dataset/relatedDoiManagement/id/<?= $model->id ?>" class="btn-green">Next</a>
    </div>
</div>

<script>
    $(".myHint").popover();
    $(".delete-title").tooltip({'placement':'top'});
    
   $(".js-add-exLink").click(function(e) {
        e.preventDefault();
        var  did = $(this).attr('dataset-id');
        var externalLinkType = $(".js-ex-link-type").val();
        var url = $(".js-ex-link-url").val();

        $.ajax({
           type: 'POST',
           url: '/adminExternalLink/addExLink',
           data:{'dataset_id': did, 'url': url, 'externalLinkType': externalLinkType},
           success: function(response){
           	if(response.success) {
           		window.location.reload();
           	} else {
           		alert(response.message);
           	}
          },
          error:function(){
      	}   
        });
    });

    $(".js-delete-exLink").click(function(e) {
    	if (!confirm('Are you sure you want to delete this item?'))
            return false; 
        e.preventDefault();
        var  exlinkid = $(this).attr('exLink-id');

        $.ajax({
           type: 'POST',
           url: '/adminExternalLink/deleteExLink',
           data:{'exLink_id': exlinkid},
           success: function(response){
           	if(response.success) {
           		window.location.reload();
           	} else {
           		alert(response.message);
           	}
          },
          error:function(){
      	}   
        });
    });
</script>