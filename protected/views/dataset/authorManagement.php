<h2>Add Authors</h2>
<div class="clear"></div>

<a href="/dataset/datasetManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Study')?></a>
<a href="#" class="btn sw-selected-btn"><?= Yii::t('app' , 'Author')?></a>
<a href="/dataset/projectManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Project')?></a>
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
					<th id="author-grid_c0" width="25%">First name</th>
					<th id="author-grid_c1" width="25%">Middle name</th>
					<th id="author-grid_c2" width="25%">Last name</th>
					<th id="author-grid_c3" width="10%">
                          <span>ORCiD</span>
                          <a class="myHint" 
                          title="ORCID provides a persistent digital identifier that distinguishes you from every other researcher.  Please visit <a href=&quot;http://orcid.org/&quot;>http://orcid.org/</a> to learn more."
                          style="float:right"
                          >
                        </a>
                        </th>
					<th id="author-grid_c4" width="10%">
                          <span>Order</span>
                          <a class="myHint" title="This is the order in which authors will appear in the dataset citation." style="float:right"></a>
                        </th>
					<th id="author-grid_c5" class="button-column" width="5%"></th>
				</tr>
			</thead>
			<tbody>
				<?php if($das) { ?>
				<?php foreach($das as $da) { ?>
				<tr class="odd">
					<td><?=$da->author->first_name?></td>
					<td><?=$da->author->middle_name?></td>
					<td><?=$da->author->surname?></td>
					<td><?=$da->author->orcid?></td>
					<td>
						<input class='js-author-rank' 
						id="js-author-rank-<?=$da->id?>"
						da-id="<?=$da->id?>" 
						value="<?=$da->rank?>"
						type="text" 
						style="width:25px">
					</td>
					<td class="button-column">
						<a class="js-delete-author delete-title" da-id="<?=$da->id?>"  title="delete this row">
							<img alt="delete this row" src="/images/delete.png">
						</a>
					</td>
				</tr>
				<? } ?>
				<? } else { ?>
				<tr>
					<td colspan="4">
						<span class="empty">No results found.</span>
					</td>
				</tr>
				<tr>
				<? } ?>
					<td>
					<input id="js-author-first-name" type="text" name="Author[first_name]" placeholder="First Name" style="width:180px">
					</td>
					<td>
					<input id="js-author-middle-name" type="text" name="Author[middle_name]" placeholder="Middle Name (Option)" style="width:180px">
					</td>
					<td>
					<input id="js-author-last-name" type="text" name="Author[last_name]" placeholder="Last Name" style="width:180px">
					</td>
					<td>
                       <input id="js-author-orcid" type="text" name="Author[orcid]" placeholder="ORCiD(Option)" style="width:100px">
                       </td>
					<td></td>
                          <td></td>
				</tr>
				</tbody>
		</table>
        </div>
       <div class="add-author-container"><a href="#" dataset-id="<?=$model->id?>" class="btn js-add-author"/>Add Author</a></div>
    </div>

     <div class="span12" style="text-align:center">
        <a href="/dataset/datasetManagement/id/<?= $model->id ?>" class="btn-green">Previous</a>
        <a href="/user/view_profile" title="Save your incomplete submission and leave the submission wizard." class="btn-green delete-title">Save & Quit</a>
        <a href="/dataset/projectManagement/id/<?= $model->id ?>" class="btn-green">Next</a>
    </div>

</div>

<script>
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

    $(".js-open-rank").click(function(e) {
    	e.preventDefault();
        	var  daid = $(this).attr('da-id');
        	$('.js-author-rank').hide();
        	$("#js-author-rank-"+daid).show();
        	$('.js-open-rank').show();
        	$(this).hide();
    });

    $(".js-author-rank").change(function(e) {
    	e.preventDefault();
        	var  daid = $(this).attr('da-id');
        	var rank = $(this).val();
        	$.ajax({
           type: 'POST',
           url: '/adminDatasetAuthor/updateRank',
	       data:{'da_id': daid,'rank':rank},
            beforeSend:function(){
               ajaxIndicatorStart('loading data.. please wait..');
            },
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

    $(".js-add-author").click(function(e) {
        e.preventDefault();
        var  did = $(this).attr('dataset-id');
        var first_name = $('#js-author-first-name').val();
        var last_name = $('#js-author-last-name').val();
        var middle_name = $('#js-author-middle-name').val();
        var orcid = $('#js-author-orcid').val();

        var author = {
        	'first_name': first_name,
        	'last_name': last_name,
        	'middle_name': middle_name,
         'orcid': orcid,
    	    }

        $.ajax({
           type: 'POST',
           url: '/adminDatasetAuthor/addAuthor',
           data:{'dataset_id': did, 'Author':author},
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
    })

    $(".js-delete-author").click(function(e) {
    	if (!confirm('Are you sure you want to delete this item?'))
            return false; 
        e.preventDefault();
        var  daid = $(this).attr('da-id');

        $.ajax({
           type: 'POST',
           url: '/adminDatasetAuthor/deleteAuthor',
           data:{'da_id': daid},
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
    })

    $(document).ajaxStop(function () {
        //hide ajax indicator
        ajaxIndicatorStop();
    });

    $(".myHint").tooltip({'placement':'top'});
    $(".delete-title").tooltip({'placement':'top'});

</script>