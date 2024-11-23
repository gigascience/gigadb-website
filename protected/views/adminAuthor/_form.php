<div class="section form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'author-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div class="row">
		<div class="col-md-12">
			<p class="note">Fields with <span class="required">*</span> are required.</p>

			<?php if ($model->hasErrors()) : ?>
				<div class="alert alert-danger">
					<?php echo $form->errorSummary($model); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'surname',
			'inputOptions' => [
				'required' => true,
				'maxlength' => 255
			],
			'groupOptions' => [
				'class' => 'col-md-4'
			],
		]);

		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'first_name',
			'inputOptions' => [
				'maxlength' => 255
			],
			'groupOptions' => [
				'class' => 'col-md-4'
			],
		]);

		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'middle_name',
			'inputOptions' => [
				'maxlength' => 255
			],
			'groupOptions' => [
				'class' => 'col-md-4'
			],
		]);

		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'custom_name',
			'inputOptions' => [
				'maxlength' => 255
			],
			'groupOptions' => [
				'class' => 'col-md-12'
			],
		]);

		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'orcid',
			'inputOptions' => [
				'maxlength' => 128
			],
			'groupOptions' => [
				'class' => 'col-md-6'
			],
		]);

		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'gigadb_user_id',
			'inputOptions' => [
				'maxlength' => 128
			],
			'groupOptions' => [
				'class' => 'col-md-6'
			],
		]);
		?>

		<div class="col-md-12">
			<div class="merge-author-info">
				<?php
				$identical_authors = $model->getIdenticalAuthors();
				if (!empty($identical_authors)) {
				?>
					<div class="alert alert-gigadb-info">
						this author is merged with author(s):
						<ul class="list-unstyled">
							<?php
							foreach ($identical_authors as $author_id) {
								$author = Author::model()->findByPk($author_id);
								echo "<li>" . $author->getAuthorDetails() . "</li>";
							}
							?>
						</ul>
					</div>
				<?php	} ?>

				<?php
				if (!empty($identical_authors)) {
					echo CHtml::link(
						'Unmerge author from those authors',
						array('adminAuthor/unmerge', 'id' => $model->id),
						array('class' => 'btn btn-link')
					);
				}
				?>
			</div>
		</div>

		<div class="col-md-12">
			<div class="pull-right btns-row">
				<?php
				if ($model->id) {
					echo CHtml::link(
						'Merge with an author',
						array('adminAuthor/prepareAuthorMerge', 'origin_author_id' => $model->id),
						array('class' => 'btn background-btn-o btn-min-width')
					);
				}
				?>
				<a href="/adminAuthor/admin" class="btn background-btn-o btn-min-width">Cancel</a>
				<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width')); ?>
			</div>
		</div>
	</div>

	<?php $this->endWidget(); ?>
</div>