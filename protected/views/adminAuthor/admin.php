<?php Yii::app()->clientScript->registerScript('helpers', 'baseUrl = '.CJSON::encode(Yii::app()->request->getBaseUrl(true)).';',CClientScript::POS_HEAD); ?>
<?php Yii::app()->clientScript->registerScript('graphreq', 'var httpRequest;',CClientScript::POS_HEAD); ?>


<p class="text-left">
<h1>Manage Authors</h1>
</p>

<?php
	$user = null;
	if ( isset(Yii::app()->session['attach_user']) ) {
		$user = User::model()->findByPk(Yii::app()->session['attach_user']) ;
	}

	$origin_author = null;
	if ( isset(Yii::app()->session['merge_author']) ) {
		$origin_author = Author::model()->findByPk(Yii::app()->session['merge_author']) ;
	}
?>
<div class="clear"></div>
<?php if (null != $user ) { ?>
	<?php
		$existing_link = Author::findAttachedAuthorByUserId($user->id);
		if (null == $existing_link) {
	?>
			<div class="alert alert-info">
				<?php echo CHtml::link('&times;', array('adminAuthor/prepareUserLink',
                   'user_id'=>$user->id,'abort'=>'yes'), array('class'=>'close', 'data-dismiss'=>'alert')); ?>
				Click on a row to proceed with linking that author with user <? echo $user->first_name . " " . $user->last_name ?></div>
	<? } else { ?>
				<div class="alert alert-warning">
				<?php echo CHtml::link('&times;', array('adminAuthor/prepareUserLink',
                   'user_id'=>$user->id,'abort'=>'yes'), array('class'=>'close', 'data-dismiss'=>'alert')); ?>
					The user <? echo $user->first_name . " " . $user->last_name ?> is already associated to author <? echo $existing_link->getDisplayName()." (".$existing_link->id.")" ?>
					</div>
	<? } ?>
<? } ?>


<?php

	if (!empty($origin_author)) {
		echo "<div class=\"alert alert-info\">";
		echo CHtml::link('&times;', array('adminAuthor/prepareAuthorMerge',
	                   'origin_author_id'=>$origin_author->id,'abort'=>'yes'), array('class'=>'close', 'data-dismiss'=>'alert'));

		echo "Click on a row to proceed with merging that author with author {$origin_author->getDisplayName()}";

		echo "</div>";
	}
?>

<div class="row">
	<div class="span3">
		<a href="/adminAuthor/create" class="btn">Create a new author</a>
</div>

</div>
<div class="row">&nbsp;</div>

<div class="row">
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'author-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-bordered',
	'selectionChanged'=>"function(id){open_controls($.fn.yiiGridView.getSelection(id));}",
	'rowHtmlOptionsExpression'=>'array("id"=>$data->id, "data-author-surname"=>$data->surname,  "data-author-firstname"=>$data->first_name,  "data-author-middlename"=>$data->middle_name,  "data-author-orcid"=>$data->orcid)',
	'filter'=>$model,
	'columns'=>array(
		'surname',
		'middle_name',
		'first_name',
		'orcid',
		//'rank',
		array('name'=> 'dois_search', 'value'=>'$data->listOfDataset'),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
</div>

<!-- Modal -->
<div id="user_link" class="modal fade">
	<div class="modal-dialog" role="document">
	    <div class="modal-content">
			<div class="modal-header">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			    <h4 class="modal-title">Confirm linking this author to the user?</h4>
			</div>
			<?php if (!empty($user)) { ?>
			<div class="modal-body">
				<table id="author_compare" class="table table-condensed table-striped table-hover table-bordered">
					<thead>
						<tr>
						  <th>&nbsp;</th>
					      <th>User to link to author</th>
					      <th>Author to be linked to user</th>
					    </tr>
					</thead>
					<tbody>
						<tr>
							<td>ID:</td>
							<td><? echo $user->id ?></td>
							<td id="target_id"></td>
						</tr>
						<tr">
							<td>Surname:</td>
							<td><? echo $user->last_name ?></td>
							<td id="target_surname"></td>
						</tr>
						<tr>
							<td>First name:</td>
							<td><? echo $user->first_name ?></td>
							<td id="target_first_name"></td>
						</tr>
						<tr>
							<td>Middle name:</td>
							<td>&nbsp;</td>
							<td id="target_middle_name"></td>
						</tr>
						<tr>
							<td>Orcid:</td>
							<td><? echo $user->orcid_id ?></td>
							<td id="target_orcid"></td>
						</tr>
						<tr>
							<td>Already merged with:</td>
							<td>n/a</td>
							<td id="target_graph"></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
					<a href="#" class="btn btn-active" title="link" onclick="link_to_author();">Link user <? echo $user->first_name . " " . $user->last_name ?> to that author</a>
				<?php echo CHtml::link('Abort and clear selected user', array('adminAuthor/prepareUserLink',
				                   'user_id'=>$user->id,'abort'=>'yes'), array('class'=>'btn btn-active')); ?>
				<a type="button" class="btn close" data-dismiss="modal" aria-hidden="true">Close</a>
			</div>
			<? } ?>
		</div>
	</div>
</div>

<!-- Modal -->
<div id="author_merge" class="modal fade">
	<div class="modal-dialog" role="document">
	    <div class="modal-content">
			<div class="modal-header">
				    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				    <h4 class="modal-title">Confirm merging these two authors?</h4>
			</div>
			<div class="modal-body">

				<?php if (!empty($origin_author)) { ?>

				<div id="merge_status" class="alert">
				</div>
				<table id="author_compare" class="table table-condensed table-striped table-hover table-bordered">
					<thead>
						<tr>
						  <th>&nbsp;</th>
					      <th>Author to merge</th>
					      <th>Author to be merged with</th>
					    </tr>
					</thead>
					<tbody>
						<tr>
							<td>ID:</td>
							<td><? echo $origin_author->id ?></td>
							<td id="target_id"></td>
						</tr>
						<tr">
							<td>Surname:</td>
							<td><? echo $origin_author->surname ?></td>
							<td id="target_surname"></td>
						</tr>
						<tr>
							<td>First name:</td>
							<td><? echo $origin_author->first_name ?></td>
							<td id="target_first_name"></td>
						</tr>
						<tr>
							<td>Middle name:</td>
							<td><? echo $origin_author->middle_name ?></td>
							<td id="target_middle_name"></td>
						</tr>
						<tr>
							<td>Orcid:</td>
							<td><? echo $origin_author->orcid ?></td>
							<td id="target_orcid"></td>
						</tr>
						<tr>
							<td>Already merged with:</td>
							<td><? echo implode(",",$origin_author->getIdenticalAuthorsDisplayName()) ?></td>
							<td id="target_graph"></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
					<a href="#" class="btn btn-active" title="link" onclick="merge_authors();">Yes, merge authors</a>
					<?php echo CHtml::link('No, abort and clear session', array('adminAuthor/prepareAuthorMerge',
				                   'origin_author_id'=>$origin_author->id,'abort'=>'yes'), array('class'=>'btn btn-active')); ?>
				    <a type="button" class="btn close" data-dismiss="modal" aria-hidden="true">Close</a>
			</div>
		</div>
	</div>
</div>

	<? } ?>

<script>
	function open_controls(author_id) {
		var want_dialog = null;
<?php
	if ( !empty($user) ) {
		echo "want_dialog = 'user_link';";
	}
	else if ( !empty($origin_author) ){
		echo "want_dialog = 'author_merge';";
	}
?>
		var author_line =  document.getElementById(author_id);
		var author_surname = author_line.getAttribute("data-author-surname");

		switch(want_dialog) {
			case 'user_link':
				$("#user_link").data('author_id', author_id);
				$('#target_id').html(author_id);
				$('#target_surname').html(author_line.getAttribute("data-author-surname"));
				$('#target_first_name').html(author_line.getAttribute("data-author-firstname"));
				$('#target_middle_name').html(author_line.getAttribute("data-author-middlename"));
				$('#target_orcid').html(author_line.getAttribute("data-author-orcid"));
			    $("#user_link").modal('show');
			    break;
			case 'author_merge':
				$("#author_merge").data('author_id', author_id);
				$("#merge_status").removeAttr("class").empty();
				$('#target_id').html(author_id);
				$('#target_surname').html(author_line.getAttribute("data-author-surname"));
				$('#target_first_name').html(author_line.getAttribute("data-author-firstname"));
				$('#target_middle_name').html(author_line.getAttribute("data-author-middlename"));
				$('#target_orcid').html(author_line.getAttribute("data-author-orcid"));
			    $("#author_merge").modal('show');
			    break;
			default:
				console.log('no modal dialog specified');
		}

	    return false;
	}

	function link_to_author() {
	<?
		echo 'var base_url = "'.Yii::app()->urlManager->createUrl('adminAuthor/linkUser',array('id'=>'')).'";'
	?>
		var author_id = $("#user_link").data('author_id');
		window.location= base_url + "/" + author_id; 
	}

	function merge_authors() {
		var orgin_author_id = null;
		var orgin_graph = null;
		<?php
			if (!empty($origin_author)) {
				echo 'origin_author_id = ' . $origin_author->id .';' ;
				echo 'origin_graph = '.CJSON::encode($origin_author->getIdenticalAuthors()).';' ;
			}
		?>
		var target_author_id = parseInt($("#author_merge").data('author_id'),10);

		if (target_author_id == origin_author_id) {
			$('#merge_status').addClass("alert").addClass("alert-error").html("Cannot merge with self. Choose another author to merge with");
		}
		else if (-1 != origin_graph.indexOf(target_author_id)) {
			$('#merge_status').addClass("alert").addClass("alert-error").html("Authors already merged. Choose another author to merge with");
		}
		else {
			window.location = baseUrl + "/adminAuthor/mergeAuthors?origin_author=" + origin_author_id + "&target_author="+ target_author_id; 
		}

	}

	function makeRequest() {
		// console.log('in makeRequest');
		var target_author_id = $("#author_merge").data('author_id');
	    httpRequest = new XMLHttpRequest();

	    if (!httpRequest) {
	    	console.log('Giving up ! Cannot create an XMLHTTP instance');
	    	return false;
	    }
	    httpRequest.onreadystatechange = populateTargetGraph;
	    httpRequest.open('GET', baseUrl +'/adminAuthor/identicalAuthorsGraph/id/' + target_author_id);
	    // console.log(baseUrl +'/adminAuthor/identicalAuthorsGraph/id/' + target_author_id);
	    httpRequest.send();
	}

	function populateTargetGraph() {
		// console.log('in populateTargetGraph');
	    if (httpRequest.readyState === XMLHttpRequest.DONE) {
		    if (httpRequest.status === 200) {
		        $('#target_graph').html(httpRequest.responseText);
		    } else {
		        console.log('There was a problem with the request.');
		    }
	    }
	}

</script>

<script>
    $('#author_merge').on('show', function () {
        makeRequest();
    });

    $('#author_merge').on('hidden', function () {
        $("#merge_status").removeAttr("class").empty();
    });
</script>
