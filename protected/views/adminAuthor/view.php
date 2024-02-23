<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'View Author #' . $model->id,
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/adminAuthor/admin'],
			['isActive' => true, 'label' => 'View'],
		]
	]);
	foreach (Yii::app()->user->getFlashes() as $key => $message) {
		echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
	}

	$user_command = UserCommand::model()->findByAttributes(array("actionable_id" => $model->id, "action_label" => "claim_author"));

	$this->widget('zii.widgets.CDetailView', array(
		'data' => $model,
		'attributes' => array(
			'id',
			'name',
			'displayName',
			'orcid',
			'gigadb_user_id',
			'rank',
		),
		'htmlOptions' => array('class' => 'table table-striped table-bordered dataset-view-table'),
		'itemCssClass' => array('odd', 'even'),
		'itemTemplate' => '<tr class="{class}"><th scope="row">{label}</th><td>{value}</td></tr>'
	));

	if (null != $user_command) {
	?>
		<div class="alert alert-gigadb-info">There is a pending claim on this author
			<div>
				<?php
				echo CHtml::link(
					'Edit user to validate/reject the claim',
					array('user/update/', 'id' => $user_command->requester_id),
					array('class' => 'btn background-btn ml-10 mr-10')
				);
				?>
			</div>
		</div>
		<?php
	}

	if (null != $model->gigadb_user_id) {
		$user = User::model()->findByPk($model->gigadb_user_id);
		if (null != $user) {
		?>
			<div class="alert alert-gigadb-info">
				<?php
				echo "this author is linked to user {$user->first_name} {$user->last_name} ({$model->gigadb_user_id})";
				?>
			</div>
	<?php
		}
	}

	?>
	<div class="merge_info">
		<?php
		$identical_authors = $model->getIdenticalAuthors();
		if (!empty($identical_authors)) {
		?>
			<div class="alert alert-info alert-gigadb-info">
				this author is merged with author(s):
				<ul class="list-unstyled mt-10">
					<?php
					foreach ($identical_authors as $author_id) {
						$author = Author::model()->findByPk($author_id);
						echo "<li>" . $author->getAuthorDetails() . "</li>";
					}
					?>
				</ul>

			</div>

		<?php	} ?>

	</div>

</div>