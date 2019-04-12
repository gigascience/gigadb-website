<h2>Add Authors</h2>
<div class="clear"></div>

<?php $this->renderPartial('_tabs_navigation', array('model' => $model)); ?>

<div class="span12 form well">
    <div class="form-horizontal">
	<div id="author-grid" class="grid-view">
        <p>Please provide all author details, to do this you may add them individually, or upload a CSV file. Once added the author details will appear in the table below and you may make any required changes directly in the table.</p>
		<table class="table table-bordered" id="author-table">
			<thead>
				<tr>
					<th id="author-grid_c0">First name</th>
					<th id="author-grid_c1">
                        <span>Middle name</span>
                        <a class="myHint"
                           data-content='Enter all middle names or initials separated by spaces, the initial of each middle name will be used in the displayed name.'
                           style="float: right">
                        </a>
                    </th>
					<th id="author-grid_c2">Last name</th>
					<th id="author-grid_c3">
                        <span>ORCiD</span>
                        <a class="myHint"
                         data-content='<a href=https://orcid.org/about/what-is-orcid/mission target=_blank>ORCID<a/> provides a persistent digital identifier that distinguishes you from every other researcher.'
                         data-html="true"
                         style="float: right;">
                        </a>
                    </th>
                    <th id="author-grid_c4">CrediT</th>
					<th id="author-grid_c5">
                        <span>Order</span>
                        <a class="myHint"
                         data-content="This is the order in which authors will appear in the dataset citation."
                         style="float: right;"></a>
                    </th>
					<th id="author-grid_c6" class="button-column" width="5%"></th>
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
					<td><?=$da->author->contribution ? $da->author->contribution->name : '' ?></td>
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
					<td colspan="7">
						<span class="empty">No results found.</span>
					</td>
				</tr>
				<tr>
				<? } ?>
					<td>
					<input id="js-author-first-name" type="text" name="Author[first_name]" placeholder="First Name" style="width:150px">
					</td>
					<td>
					<input id="js-author-middle-name" type="text" name="Author[middle_name]" placeholder="Middle Name (optional)" style="width:150px">
					</td>
					<td>
					<input id="js-author-last-name" type="text" name="Author[last_name]" placeholder="Last Name" style="width:150px">
					</td>
					<td>
                       <input id="js-author-orcid" type="text" pattern="[1-9]{4}-[1-9]{4}-[1-9]{4}-[1-9]{4}" name="Author[orcid]" placeholder="ORCiD (optional)" style="width:130px">
                    </td>
                    <td>
                        <input id="js-author-contribution" type="text" name="Author[contribution]" placeholder="Contribution" style="width:120px">
                    </td>
					<td colspan="2"><a href="#" dataset-id="<?=$model->id?>" class="btn js-add-author"/>Add Author</a></td>
				</tr>
				</tbody>
		</table>
        </div>

        <p style="text-align: center">OR</p>

       <div class="add-author-container">
           <label for="authors">author list upload</label>
           <a class="myHint" data-content="You may upload a tabular file of authors names (in TSV or CSV format), please use include 5 columns and 1 row for each author, e.g.<br>
Firstname	Middlename	Lastname	ORCID 		contribution<br>
Rosalind	Elsie	Franklin 	0000-0000-0000-0001	Conceptualization"
              data-html="true" style="float: none"></a>
           <input type="file" id="authors" name="authors">
           <a href="#" dataset-id="<?=$model->id?>" class="btn js-add-authors"/>Add Authors</a>
       </div>
    </div>

     <div style="text-align:center">
        <a href="/datasetSubmission/study/id/<?= $model->id ?>" class="btn-green">Previous</a>
         <a href="/user/view_profile" class="btn-green">Save</a>
        <a href="/datasetSubmission/projectManagement/id/<?= $model->id ?>" class="btn-green">Next</a>
    </div>

</div>

<script>
    $("#js-author-orcid").keypress(function(){
        var input = $(this);

        setTimeout((function(){
            var val = input.val();
            var valLength = val.length;

            var lastChar = val.slice(-1);

            if (valLength > 19) {
                input.val(val.slice(0, 19));
                return false;
            }

            if (valLength == 5 || valLength == 10 || valLength == 15) {
                if (lastChar == parseInt(lastChar)) {
                    lastChar = '-' + lastChar;
                } else {
                    lastChar = lastChar.replace(/[^-]/g, '');
                }
            } else {
                lastChar = lastChar.replace(/[^0-9]/g, '');
            }

            var withoutLastChar = val.slice(0, -1);
            withoutLastChar = withoutLastChar.replace(/[^0-9\-]/g, '');
            input.val(withoutLastChar + lastChar);

        }), 50);
    });
</script>
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
        var contribution = $('#js-author-contribution').val();

        var author = {
        	'first_name': first_name,
        	'last_name': last_name,
        	'middle_name': middle_name,
            'orcid': orcid,
            'contribution': contribution,
        };

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
            error: function(xhr) {
                alert(xhr.responseText);
            }
        });
    });

    $(".js-add-authors").click(function() {
        var  did = $(this).attr('dataset-id');

        var data = new FormData();
        data.append("authors", $("#authors")[0].files[0]);
        data.append("dataset_id", did);

        $.ajax({
            url: '/adminDatasetAuthor/addAuthors',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
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

        return false;
    });

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
    });

    $(document).ajaxStop(function () {
        //hide ajax indicator
        ajaxIndicatorStop();
    });

    $(".delete-title").tooltip({'placement':'top'});
</script>