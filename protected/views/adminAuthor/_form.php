<div class="section form row">

	<div class="col-md-offset-3 col-md-6">
		<?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'author-form',
			'enableAjaxValidation' => false,
		)); ?>

		<p class="note">Fields with <span class="required">*</span> are required.</p>

		<?php if ($model->hasErrors()) : ?>
			<div class="alert alert-danger">
				<?php echo $form->errorSummary($model); ?>
			</div>
		<?php endif; ?>

		<?php
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'surname',
			'inputOptions' => [
				'required' => true,
				'maxlength' => 255
			],
		]);

		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'first_name',
			'inputOptions' => [
				'maxlength' => 255
			],
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'middle_name',
			'inputOptions' => [
				'maxlength' => 255
			],
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'custom_name',
			'inputOptions' => [
				'maxlength' => 255
			],
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'orcid',
			'inputOptions' => [
				'maxlength' => 128
			],
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'gigadb_user_id',
			'inputOptions' => [
				'maxlength' => 128
			],
		]);
		?>

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

		<div class="pull-right">
			<?php
			echo CHtml::link(
				'Merge with an author',
				array('adminAuthor/prepareAuthorMerge', 'origin_author_id' => $model->id),
				array('class' => 'btn background-btn-o')
			);
			?>
			<a href="/adminAuthor/admin" class="btn background-btn-o">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
		</div>

		<?php $this->endWidget(); ?>
	</div>

</div>