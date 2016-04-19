<h2>Add Relationship with another DOI?</h2>
<div class="clear"></div>

<a href="/dataset/datasetManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Study')?></a>
<a href="/dataset/authorManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Author')?></a>
<a href="/dataset/projectManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Project')?></a>
<a href="/dataset/linkManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Link')?></a>
<a href="/dataset/exLinkManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'External Link')?></a>
<a href="#" class="btn sw-selected-btn"><?= Yii::t('app' , 'Related Doi')?></a>
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
					<th id="author-grid_c0" width="45%">Related DOI</th>
					<th id="author-grid_c0" width="45%">Relationship</th>
					<th id="author-grid_c5" class="button-column" width="10%"></th>
				</tr>
			</thead>
			<tbody>
                   <?php if($relations) { ?>
				<?php foreach($relations as $relation) { ?>
				<tr class="odd" id="js-relation-<?=$relation->id?>">
					<td><?= $relation->related_doi ?></td>
					<td><?= $relation->relationship->name ?></td>
					<td class="button-column">
						<a class="js-delete-relation delete-title" relation-id="<?=$relation->id?>"  title="delete this row">
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

          <p class="note">If your dataset is directly related to any other GigaDB published DOI,
                please add the DOI number here and the type of the relationship.</p>

            <div class="control-group">
                <label class='control-label'>Related Doi</label>
                <a class="myHint" data-content="Use the six digit GigaDB identifier e.g. 100023"></a>
                <div class="controls">
                    <?= CHtml::dropDownList('relation', null, CHtml::listData(Util::getDois(), 'identifier', 'identifier'),array('class'=>'js-relation-doi','style'=>'width:250px')); ?>
                </div>
            </div>

            <div class="control-group">
                <label class='control-label'>Relationship</label>
                <a class="myHint" data-content="Please select relationship type from drop down menu where:<br/>
                   IsNewVersionOf = your submission is a new version of that DOIs<br/>
                   IsSupplentedBy = Your submission is supplemented by existing DOI<br/>
                   IsSupplementTo = Your submission is supplemental to an existing DOI<br/>
                   Compiles = your submission is used to create the data in existing DOI<br/>
                   IsCompiledBy = your data was created by using software in existing DOI"></a>
                <div class="controls">
                   <?= CHtml::dropDownList('relation', null, CHtml::listData(Relationship::model()->findAll(), 'id', 'name'),array('class'=>'js-relation-relationship','style'=>'width:250px')); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="span12" style="text-align:center">
                    <a href="#" dataset-id="<?=$model->id?>" class="btn js-add-relation"/>Add Related Doi</a>
                </div>
            </div>

    </div>

     <div class="span12" style="text-align:center">
        <a href="/dataset/exLinkManagement/id/<?= $model->id ?>" class="btn-green">Previous</a>
        <a href="/user/view_profile" title="Save your incomplete submission and leave the submission wizard." class="btn-green delete-title">Save & Quit</a>
        <a href="/dataset/SampleManagement/id/<?= $model->id ?>" class="btn-green">Next</a>
    </div>
</div>

<script>
$(".delete-title").tooltip({'placement':'top'});

function ajaxIndicatorStart(text)
{
    if($('body').find('#resultLoading').attr('id') != 'resultLoading'){
    $('body').append('<div id="resultLoading" style="display:none"><div><img width="30" src="/images/ajax-loader.gif"><div>'+text+'</div></div><div class="bg"></div></div>');
    }

    $('#resultLoading').css({
        'width':'100%',
        'height':'100%',
        'position':'fixed',
        'z-index':'10000000',
        'top':'0',
        'left':'0',
        'right':'0',
        'bottom':'0',
        'margin':'auto'
    });

    $('#resultLoading .bg').css({
        'background':'#000000',
        'opacity':'0.7',
        'width':'100%',
        'height':'100%',
        'position':'absolute',
        'top':'0'
    });

    $('#resultLoading>div:first').css({
        'width': '250px',
        'height':'75px',
        'text-align': 'center',
        'position': 'fixed',
        'top':'0',
        'left':'0',
        'right':'0',
        'bottom':'0',
        'margin':'auto',
        'font-size':'16px',
        'z-index':'10',
        'color':'#ffffff'

    });

    $('#resultLoading .bg').height('100%');
    $('#resultLoading').fadeIn(300);
    $('body').css('cursor', 'wait');
}

function ajaxIndicatorStop()
{
    $('#resultLoading .bg').height('100%');
    $('#resultLoading').fadeOut(300);
    $('body').css('cursor', 'default');
}

   $(".myHint").popover();

   $(".js-add-relation").click(function(e) {
        e.preventDefault();
        var  did = $(this).attr('dataset-id');
        var doi = $('.js-relation-doi').val();
        var relationship = $('.js-relation-relationship').val();

        $.ajax({
           type: 'POST',
           url: '/adminRelation/addRelation',
           data:{'dataset_id': did, 'doi': doi, 'relationship': relationship},
           beforeSend:function(){
               ajaxIndicatorStart('loading data.. please wait..');
            },
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

    $(".js-delete-relation").click(function(e) {
    	if (!confirm('Are you sure you want to delete this item?'))
            return false; 
        e.preventDefault();
        var  relationid = $(this).attr('relation-id');

        $.ajax({
           type: 'POST',
           url: '/adminRelation/deleteRelation',
           data:{'relation_id': relationid},
           beforeSend:function(){
               ajaxIndicatorStart('loading data.. please wait..');
            },
           success: function(response){
           	if(response.success) {
           		$('#js-relation-'+relationid).remove();
           	} else {
           		alert(response.message);
           	}
          },
          error:function(){
      	}   
        });
    });

    $(document).ajaxStop(function () {
        //hide ajax indicator
        ajaxIndicatorStop();
    });
</script>