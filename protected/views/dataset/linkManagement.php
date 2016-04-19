<h2>Cross reference data</h2>
<div class="clear"></div>

<a href="/dataset/datasetManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Study')?></a>
<a href="/dataset/authorManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Author')?></a>
<a href="/dataset/projectManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Project')?></a>
<a href="#" class="btn sw-selected-btn"><?= Yii::t('app' , 'Link')?></a>
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
					<th id="author-grid_c0" width="45%">Link Type</th>
					<th id="author-grid_c0" width="45%">Link</th>
					<th id="author-grid_c5" class="button-column" width="10%"></th>
				</tr>
			</thead>
			<tbody>
				<?php if($links) { ?>
				<?php foreach($links as $link) { ?>
				<tr class="odd">
					<td><?= ($link->is_primary)?  "ext_acc_mirror" : "ext_acc_link" ?></td>
					<td><?= $link->link ?></td>
					<td class="button-column">
						<a class="js-delete-link delete-title" link-id="<?=$link->id?>"  title="delete this row">
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

         <p class="note">Please add the database 
                and accession numbers of any data in this
                dataset that is also stored in other repositories,
                e.g. sequences in the Sequence Read Archive (SRA).</p>

            <div class="control-group">
                <label class='control-label'>Database</label>
                <a class="myHint" data-content="Please contact <a href=&quot;mailto:database@gigasciencejournal.com&quot; >database@gigasciencejournal.com</a> to request the addition of a new database."></a>
                <div class="controls">
                    <?= CHtml::dropDownList('link', 
                      null, 
                      CHtml::listData($link_database,'prefix','prefix'),
                      array('class'=>'js-database', 'style'=>'width:250px')); 
                    ?>
                </div>
            </div>

            <div class="control-group">
                <label class='control-label'>Accession number</label>
                <a class="myHint" data-content="Please provide unique identifier of linked data, e.g. an SRA accession; SRS012345."></a>
                <div class="controls">
		<?= CHtml::textField('link', '', array('class'=>'js-acc-num', 'size' => 60, 'maxlength' => 100, 'style'=>'width:240px', 'placeholder'=>"Unique identifier of linked data")); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="span12" style="text-align:center">
                    <a href="#" dataset-id="<?=$model->id?>" class="btn js-add-link"/>Add Link</a>
                </div>
            </div>

    </div>

     <div class="span12" style="text-align:center">
        <a href="/dataset/projectManagement/id/<?= $model->id ?>" class="btn-green">Previous</a>
        <a href="/user/view_profile" title="Save your incomplete submission and leave the submission wizard." class="btn-green delete-title">Save & Quit</a>
        <a href="/dataset/exLinkManagement/id/<?= $model->id ?>" class="btn-green">Next</a>
    </div>
</div>

<script>
   $(".myHint").popover();
   $(".delete-title").tooltip({'placement':'top'});

   $(".js-add-link").click(function(e) {
        e.preventDefault();
        var  did = $(this).attr('dataset-id');
        var database = $(".js-database :selected").text();
        var accNum = $(".js-acc-num").val();

        $.ajax({
           type: 'POST',
           url: '/adminLink/addLink',
           data:{'dataset_id': did, 'database': database, 'acc_num': accNum},
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

    $(".js-delete-link").click(function(e) {
    	if (!confirm('Are you sure you want to delete this item?'))
            return false; 
        e.preventDefault();
        var  linkid = $(this).attr('link-id');

        $.ajax({
           type: 'POST',
           url: '/adminLink/deleteLink',
           data:{'link_id': linkid},
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