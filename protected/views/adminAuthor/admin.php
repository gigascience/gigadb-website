<?php Yii::app()->clientScript->registerScript('helpers', 'baseUrl = ' . CJSON::encode(Yii::app()->request->getBaseUrl(true)) . ';', CClientScript::POS_HEAD); ?>
<?php Yii::app()->clientScript->registerScript('graphreq', 'var httpRequest;', CClientScript::POS_HEAD); ?>

<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Authors',
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
		]
	]);
	?>

	<?php
	$user = null;
	if (isset(Yii::app()->session['attach_user'])) {
		$user = User::model()->findByPk(Yii::app()->session['attach_user']);
	}

	$origin_author = null;
	if (isset(Yii::app()->session['merge_author'])) {
		$origin_author = Author::model()->findByPk(Yii::app()->session['merge_author']);
	}
	?>
	<?php if (null != $user) { ?>
		<?php
		$existing_link = Author::findAttachedAuthorByUserId($user->id);
		if (null == $existing_link) {
		?>
			<div class="alert alert-gigadb-info alert-flex">
				<span>
					Click on a row or on the <span class="fa fa-link fa-lg" ></span> button to proceed with linking that author with user <? echo $user->first_name . " " . $user->last_name ?>
				</span>
				<?php echo CHtml::link('&times;', array(
					'adminAuthor/prepareUserLink',
					'user_id' => $user->id, 'abort' => 'yes'
				), array('class' => 'close close-btn', 'data-dismiss' => 'alert', 'aria-label' => 'close')); ?>
			</div>
		<? } else { ?>
			<div class="alert alert-warning alert-flex">
				<span>
					The user <? echo $user->first_name . " " . $user->last_name ?> is already associated to author <? echo $existing_link->getDisplayName() . " (" . $existing_link->id . ")" ?>
				</span>
				<?php echo CHtml::link('&times;', array(
					'adminAuthor/prepareUserLink',
					'user_id' => $user->id, 'abort' => 'yes'
				), array('class' => 'close close-btn', 'data-dismiss' => 'alert', 'aria-label' => 'close')); ?>
			</div>
		<? } ?>
	<? } ?>

  <?php if (!empty($origin_author)) { ?>
    <div class="alert alert-gigadb-info alert-flex">
    <span>Click on a row or on the <span class="fa fa-compress fa-lg" ></span> button to proceed with merging that author with author <?php echo $origin_author->getDisplayName(); ?></span>

    <?php echo CHtml::link('&times;', array('adminAuthor/prepareAuthorMerge', 'origin_author_id' => $origin_author->id, 'abort' => 'yes'), array('class' => 'close close-btn', 'data-dismiss' => 'alert', 'aria-label' => 'close')); ?>

    </div>
  <?php } ?>

	<div class="mb-10">
		<a href="/adminAuthor/create" class="btn background-btn">Create a new author</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>

	<?php
    $actionButtons = array(
        'view' => array(
            'imageUrl' => false,
            'label' => '',
            'options' => array(
                "title" => "View",
                "class" => "fa fa-eye fa-lg icon icon-view",
                "aria-label" => "View"
            ),
        ),
        'update' => array(
            'imageUrl' => false,
            'label' => '',
            'options' => array(
                "title" => "Update",
                "class" => "fa fa-pencil fa-lg icon icon-update",
                "aria-label" => "Update"
            ),
        ),
        'delete' => array(
            'imageUrl' => false,
            'label' => '',
            'options' => array(
                "title" => "Delete",
                "class" => "fa fa-trash fa-lg icon icon-delete",
                "aria-label" => "Delete"
            ),
        ),
    );
    $template = '{view}{update}{delete}';
    $headerStyle = 'width: 100px';

    if (!empty($origin_author)) {
      $actionButtons['merge_authors'] = array(
        'imageUrl' => false,
        'label' => '',
        'options' => array(
            "title" => "Merge authors",
            "class" => "fa fa-compress fa-lg icon icon-merge",
            "aria-label" => "Merge authors",
            "role" => "button",
        ),
        "click" => "handleLinkOrMergeClick",
      );
      $template = '{view}{update}{delete}{merge_authors}';
      $headerStyle = 'width: 120px';
    }

    if (null != $user) {
      $actionButtons['link_user'] = array(
        'imageUrl' => false,
        'label' => '',
        'options' => array(
            "title" => "Link user ot this author",
            "class" => "fa fa-link fa-lg icon icon-link",
            "aria-label" => "Link user to this author",
            "role" => "button",
        ),
        "click" => "handleLinkOrMergeClick",
      );
      $template = '{view}{update}{delete}{link_user}';
      $headerStyle = 'width: 120px';
    }

    $this->widget('CustomGridView', array(
		'id' => 'author-grid',
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'selectionChanged' => "function(id){open_controls($.fn.yiiGridView.getSelection(id));}",
		'rowHtmlOptionsExpression' => 'array("id"=>$data->id, "data-author-surname"=>$data->surname,  "data-author-firstname"=>$data->first_name,  "data-author-middlename"=>$data->middle_name,  "data-author-orcid"=>$data->orcid)',
		'filter' => $model,
		'columns' => array(
			'surname',
			'middle_name',
			'first_name',
			'orcid',
			//'rank',
			array('name' => 'dois_search', 'value' => '$data->listOfDataset', 'headerHtmlOptions' => array('style' => 'width: 120px')),
			array(
        'class' => 'CButtonColumn',
        'header' => "Actions",
        'headerHtmlOptions' => array('style' => $headerStyle),
        'template' => $template,
        'buttons' => $actionButtons,
      )
		),
	)); ?>

</div>

<!-- Modal -->
<div id="user_link" class="modal fade">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close modal-close-btn" data-dismiss="modal" aria-label="close">&times;</button>
				<h2 class="h4 modal-title">Confirm linking this author to the user?</h2>
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
				<div class="modal-footer modal-footer-flex">
					<a href="#" class="btn btn-active" title="link" onclick="link_to_author();">Link user <? echo $user->first_name . " " . $user->last_name ?> to that author</a>
					<?php echo CHtml::link('Abort and clear selected user', array(
						'adminAuthor/prepareUserLink',
						'user_id' => $user->id, 'abort' => 'yes'
					), array('class' => 'btn btn-active')); ?>
					<button class="btn modal-close-btn" data-dismiss="modal" aria-label="close">Close</button>
				</div>
			<? } ?>
		</div>
	</div>
</div>

<!-- Modal -->
<div id="author_merge" class="modal fade">
	<div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="authorMergeDialogTitle" tabindex="-1" id="authorMergeDialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="close dialog">&times;</button>
				<h4 class="modal-title" id="authorMergeDialogTitle">Confirm merging these two authors?</h4>
			</div>
			<div class="modal-body">

				<?php if (!empty($origin_author)) { ?>

					<div id="merge_status" class="alert">
					</div>
					<table id="author_compare" class="table table-condensed table-striped table-hover table-bordered">
						<thead>
							<tr>
								<td>&nbsp;</td>
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
									<td><? echo implode(",", $origin_author->getIdenticalAuthorsDisplayName()) ?></td>
									<td id="target_graph"></td>
								</tr>
						</tbody>
					</table>
			</div>
			<div class="modal-footer btns-row btns-row-end">
				<button type="button" class="btn background-btn" title="link" onclick="merge_authors();">Yes, merge authors</button>
				<?php echo CHtml::link('No, abort and clear session', array(
						'adminAuthor/prepareAuthorMerge',
						'origin_author_id' => $origin_author->id, 'abort' => 'yes'
					), array('class' => 'btn background-btn-o')); ?>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<? } ?>

<script>
  function handleLinkOrMergeClick(e) {
    const authorId = String($(e.target).closest('tr').attr('id'));
    open_controls(authorId);
  }
	function open_controls(author_id) {
		var want_dialog = null;
		<?php
		if (!empty($user)) {
			echo "want_dialog = 'user_link';";
		} else if (!empty($origin_author)) {
			echo "want_dialog = 'author_merge';";
		}
		?>

		var author_line = document.getElementById(author_id);

    // Prevent bug where clicking twice the same row errors out
    if (!author_line) {
      return false;
    }

		var author_surname = author_line.getAttribute("data-author-surname");

		switch (want_dialog) {
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
		echo 'var base_url = "' . Yii::app()->urlManager->createUrl('adminAuthor/linkUser', array('id' => '')) . '";'
		?>
		var author_id = $("#user_link").data('author_id');
		window.location = base_url + "/" + author_id;
	}

	function merge_authors() {
		var orgin_author_id = null;
		var orgin_graph = null;
		<?php
		if (!empty($origin_author)) {
			echo 'origin_author_id = ' . $origin_author->id . ';';
			echo 'origin_graph = ' . CJSON::encode($origin_author->getIdenticalAuthors()) . ';';
		}
		?>
		var target_author_id = parseInt($("#author_merge").data('author_id'), 10);

		if (target_author_id == origin_author_id) {
			$('#merge_status').addClass("alert").addClass("alert-error").html("Cannot merge with self. Choose another author to merge with");
		} else if (-1 != origin_graph.indexOf(target_author_id)) {
			$('#merge_status').addClass("alert").addClass("alert-error").html("Authors already merged. Choose another author to merge with");
		} else {
			window.location = baseUrl + "/adminAuthor/mergeAuthors?origin_author=" + origin_author_id + "&target_author=" + target_author_id;
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
		httpRequest.open('GET', baseUrl + '/adminAuthor/identicalAuthorsGraph/id/' + target_author_id);
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

<?php
$jsFile = Yii::getPathOfAlias('application.js.trap-focus') . '.js';
$jsUrl = Yii::app()->assetManager->publish($jsFile);
Yii::app()->clientScript->registerScriptFile($jsUrl, CClientScript::POS_END);
?>

<script>
	$('#author_merge').on('shown.bs.modal', function() {
    lastFocusedElement = document.activeElement;

    $('#authorMergeDialog').focus();
    trapFocus($(this));
		makeRequest();
	});

	$('#author_merge').on('hidden.bs.modal', function() {
		$("#merge_status").removeAttr("class").empty();
    $(this).off('keydown');
    if (lastFocusedElement) {
        lastFocusedElement.focus();
    }
	});
</script>