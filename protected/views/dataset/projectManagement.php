<h2>Part of a project?</h2>
<div class="clear"></div>

<a href="/dataset/datasetManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Study')?></a>
<a href="/dataset/authorManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Author')?></a>
<a href="#" class="btn sw-selected-btn"><?= Yii::t('app' , 'Project')?></a>
<a href="/dataset/linkManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Link')?></a>
<a href="/dataset/exLinkManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'External Link')?></a>
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
					<th id="author-grid_c0" width="80%">Project Name</th>
					<th id="author-grid_c5" class="button-column" width="20%"></th>
				</tr>
			</thead>
			<tbody>
				<?php if($dps) { ?>
				<?php foreach($dps as $dp) { ?>
				<tr class="odd">
					<td><?=$dp->project->name?></td>
					<td class="button-column">
						<a class="js-delete-project delete-title" dp-id="<?=$dp->id?>"  title="delete this row">
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
        <p class="note">If your dataset is part of a
                larger collaborative international project, 
                please select it from the list below.</p>
            <br/>

        <div class="control-group">
	        	<label class='control-label'>Project</label>
	        	<a class="myHint" data-content="Please contact <a href=&quot;mailto:database@gigasciencejournal.com&quot;>database@gigasciencejournal.com</a> to request the addition of a new project."></a>
	         <div class="controls">
	    		<?= CHtml::dropDownList('project', null, CHtml::listData(Project::model()->findAll(), 'id', 'name'),array('class'=>'js-project','style'=>'width:auto')); ?>
	        </div>
        </div>

        <div class="control-group">
            <div class="span12" style="text-align:center">
                <a href="#" dataset-id="<?=$model->id?>" class="btn js-add-project"/>Add Project</a>
            </div>
        </div>

    </div>

     <div class="span12" style="text-align:center">
        <a href="/dataset/authorManagement/id/<?= $model->id ?>" class="btn-green">Previous</a>
        <a href="/user/view_profile" title="Save your incomplete submission and leave the submission wizard." class="btn-green delete-title">Save & Quit</a>
        <a href="/dataset/linkManagement/id/<?= $model->id ?>" class="btn-green">Next</a>
    </div>
</div>

<script>
   $(".myHint").popover();
   $(".delete-title").tooltip({'placement':'top'});

   $(".js-add-project").click(function(e) {
        e.preventDefault();
        var  did = $(this).attr('dataset-id');
        var pid = $('.js-project').val();

        $.ajax({
           type: 'POST',
           url: '/adminDatasetProject/addProject',
           data:{'dataset_id': did, 'project_id':pid},
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

    $(".js-delete-project").click(function(e) {
    	if (!confirm('Are you sure you want to delete this item?'))
            return false; 
        e.preventDefault();
        var  dpid = $(this).attr('dp-id');

        $.ajax({
           type: 'POST',
           url: '/adminDatasetProject/deleteProject',
           data:{'dp_id': dpid},
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