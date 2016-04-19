<h2>Add sample details</h2>
<div class="clear"></div>

<a href="/dataset/datasetManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Study')?></a>
<a href="/dataset/authorManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Author')?></a>
<a href="/dataset/projectManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Project')?></a>
<a href="/dataset/linkManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Link')?></a>
<a href="/dataset/exLinkManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'External Link')?></a>
<a href="/dataset/relatedDoiManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Related Doi')?></a>
<a href="#" class="btn sw-selected-btn"><?= Yii::t('app' , 'Sample')?></a>
<? if($model->isProteomic) { ?>
<a href="/dataset/pxInfoManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'PX Info')?></a>
<? } ?>
<? if($model->files && count($model->files) > 0) { ?>
<a href="/adminFile/create1/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'File')?></a>
<? } ?>

<div class="span12 form well">
  <p>
  Please provide the details of all samples represented by the data being submitted, this should include their taxonomic identification, and as much information about the sample and its collection as possible.
</p>
    <div class="form-horizontal">
  <div id="author-grid" class="grid-view">

    <table class="table table-bordered sample-tab-table">
      <thead>
        <tr>
          <th class="author-grid_c0" width="15%">Sample ID</th>
          <th class="author-grid_c1" width="15%">Species name</th>
          <th class="author-grid_c2" width="20%">Attribute Name</th>
          <th class="author-grid_c2" width="20%">Attribute Value</th>
          <th class="author-grid_c2" width="20%">Attribute Unit</th>
          <th class="author-grid_c2" width="5%"></th>
          <th class="author-grid_c2" width="5%"></th>
        </tr>
      </thead>
      <tbody>
        <?php if($dss) { ?>
        <?php foreach($dss as $ds) { ?>
        <tr>
          <?php $count = count($ds->sample->sampleAttributes)+2; ?>
          <td rowspan="<?=$count?>"><?=$ds->sample->name?></td>
          <td rowspan="<?=$count?>"><?=$ds->sample->species->scientific_name?></td>
          <th class="hidden-sample-title"></th>
          <th class="hidden-sample-title"></th>
          <th class="hidden-sample-title"></th>
          <th class="hidden-sample-title"></th>
          <td rowspan="<?=$count?>" class="button-column">
            <a class="js-delete-sample delete-title" ds-id="<?=$ds->id?>"  title="delete this sample">
              <img alt="delete this row" src="/images/delete.png">
            </a>
          </td>

          <?php foreach($ds->sample->sampleAttributes as $sa) { ?>
                <tr>
                  <td><?= $sa->attribute->attribute_name ?></td>
                  <td>
                    <input class='js-sa-value' 
                    id="js-sa-value-<?=$sa->id?>"
                    sa-id="<?=$sa->id?>" 
                    value="<?=$sa->value?>"
                    type="text"
                    style="width:150px" 
                    >
                  </td>
                  <td><?= ($sa->unit)? $sa->unit->name : "" ?></td>
                  <td class="button-column">
                    <a class="js-delete-sample-attr delete-title" sa-id="<?=$sa->id?>" title="delete this sample attribute">
                      <img alt="delete this row" src="/images/delete.png">
                    </a>
                  </td>
                </tr>
          <? } ?>
                <tr>
                  <td>
                    <?php // echo CHtml::dropDownList('sampleAttr-attr', null, CHtml::listData(Attribute::model()->findAll(array('order'=>'attribute_name asc')), 'id', 'attribute_name'),array('id'=>'js-sample-attr-attr-'.$ds->sample->id,'style'=>'width:70px')); ?>
                    <?php 
                      $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                          'name' => 'name',
                          'model' => null,
                          'attribute' => 'species',
                          'source' => $this->createUrl('/adminDatasetSample/attributesList'),
                          'options' => array(
                              'minLength' => '3',
                          ),
                          'htmlOptions' => array(
                              'placeholder' => 'Attribute name',
                              'size' => 'auto',
                              'id'=>'js-sample-attr-attr-'.$ds->sample->id,
                              'style'=>'width:150px'
                          ),
                      ));
                      ?>
                  </td>
                  <td>
                    <input id="js-sample-attr-value-<?=$ds->sample->id?>" type="text" placeholder="Attribute Value" style="width:150px">
                  </td>
                  <td>
                    <?= CHtml::dropDownList('sampleAttr-unit', null, CHtml::listData(Unit::model()->findAll(array('order'=>'name asc')), 'id', 'name'),array('empty'=>'','id'=>'js-sample-attr-unit-'.$ds->sample->id,'style'=>'width:70px')); ?>
                  </td>
                  <td>
                    <a sample-id="<?=$ds->sample->id?>" class="btn js-add-sample-attr"/>Add</a>
                  </td>
                </tr>
        </tr>
        <? } ?>
        <? } else { ?>
        <tr>
          <td colspan="4">
            <span class="empty">No results found.</span>
          </td>
        </tr>
        <? } ?>
        <tr>
          <td>
          <input class="js-sample-name" type="text" name="Sample[Name]" placeholder='Sample ID'>
          </td>
          <td>
          <?php 
            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                'name' => 'name',
                'model' => null,
                'attribute' => 'species',
                'source' => $this->createUrl('/adminDatasetSample/autocomplete'),
                'options' => array(
                    'minLength' => '4',
                ),
                'htmlOptions' => array(
                    'placeholder' => 'Species name',
                    'size' => 'auto',
                    'class'=>'js-species',
                ),
            ));
            ?>
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        </tbody>
    </table>
        </div>

        <div class="control-group">
                <div class="span12" style="text-align:center">
                    <a href="#" dataset-id="<?=$model->id?>" class="btn js-add-sample"/>Add Sample</a>
                </div>
            </div>

    </div>

     <div class="span12" style="text-align:center">
        <a href="/dataset/relatedDoiManagement/id/<?= $model->id ?>" class="btn-green">Previous</a>
        <a href="/user/view_profile" title="Save your incomplete submission and leave the submission wizard." class="btn-green delete-title delete-title">Save & Quit</a>
        <? if($model->isProteomic) { ?>
        <a href="/dataset/PxInfoManagement/id/<?= $model->id ?>" class="btn-green">Next</a>
        <? } else if($model->isIncomplete) { ?>
        <a class="btn-green delete-title" title="Click submit to send information to a curator for review." href="/dataset/submit/id/<?= $model->id ?>">Submit</a>
        <? } ?>
    </div>
</div>

<script>
   $(".myHint").popover();
   $(".delete-title").tooltip({'placement':'top'});

   $(".js-sa-value").change(function(e) {
          e.preventDefault();
          var said = $(this).attr('sa-id');
          var sa_value = $(this).val();
          $.ajax({
           type: 'POST',
           url: '/adminDatasetSample/updateSampleAttribute',
          data:{'sa_id': said,'sa_value':sa_value},
           success: function(response){
            if(response.success == true) {
              window.location.reload();
            } else {
              alert(response.message);
            }
          },
          error:function(){
        }   
        });
    });

   $(".js-add-sample-attr").click(function(e) {
        e.preventDefault();
        var  sid = $(this).attr('sample-id');
        var attrId = $('#js-sample-attr-attr-'+sid).val();
        var attrValue = $('#js-sample-attr-value-'+sid).val();
        var attrUnit = $('#js-sample-attr-unit-'+sid).val();

        $.ajax({
           type: 'POST',
           url: '/adminDatasetSample/addSampleAttr',
           data:{'sample_id': sid, 'attr_id': attrId, 'attr_value': attrValue, 'attr_unit': attrUnit},
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

    $(".js-delete-sample-attr").click(function(e) {
      if (!confirm('Are you sure you want to delete this item?'))
            return false; 
        e.preventDefault();
        var  said = $(this).attr('sa-id');

        $.ajax({
           type: 'POST',
           url: '/adminDatasetSample/deleteSampleAttr',
           data:{'sa_id': said},
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

    $(".js-delete-sample").click(function(e) {
    	if (!confirm('Are you sure you want to delete this item?'))
            return false; 
        e.preventDefault();
        var  dsid = $(this).attr('ds-id');

        $.ajax({
           type: 'POST',
           url: '/adminDatasetSample/deleteSample',
           data:{'ds_id': dsid},
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

    $(".js-add-sample").click(function(e) {
        e.preventDefault();
        var  did = $(this).attr('dataset-id');
        var sampleName = $('.js-sample-name').val();
        var species = $('.js-species').val();

        $.ajax({
           type: 'POST',
           url: '/adminDatasetSample/addSample',
           data:{'dataset_id': did, 'sample_name': sampleName, 'species': species},
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