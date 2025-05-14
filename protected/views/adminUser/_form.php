 <div class="content col-md-offset-2 col-md-8">

 	<?php
		foreach (Yii::app()->user->getFlashes() as $key => $message) {
			echo '<div class="alert alert-' . $key . '">' . $message . "</div>\n";
		}
		?>
 	<?php
		$user_command = UserCommand::model()->findByAttributes(array("requester_id" => $model->id, "status" => "pending"));
		$linked_author = $model->id ? Author::findAttachedAuthorByUserId($model->id) : null;
    ?>

 	<?php
		if (!$model->isNewRecord) {
			if ($user_command) {
				echo CHtml::openTag('div', array('class' => 'subsection'));
				$claimed_author = Author::model()->findByPk($user_command->actionable_id);
				$message = "This user has a pending claim on author " . $claimed_author->getDisplayName();
				$validate_link = CHtml::link(
					'Validate',
					array('AdminUserCommand/validate', 'id' => $user_command->id),
					array('class' => 'btn background-btn')
				);
				$reject_link = CHtml::link(
					'Reject',
					array('AdminUserCommand/reject', 'id' => $user_command->id),
					array('class' => 'btn background-btn-o')
				);
				$author_link = CHtml::link(
					'Author info',
					array('AdminAuthor/view', 'id' => $user_command->actionable_id),
					array('class' => 'btn background-btn-o')
				);
				echo CHtml::closeTag('div');
		?>
 			<div class="alert alert-gigadb-info">
 				<div class="mb-10">
 					<? echo $message ?>
 				</div>
 				<div class="btn-toolbar">
 					<? echo $validate_link ?>
 					<? echo $reject_link ?>
 					<? echo $author_link ?>
 				</div>
 			</div>

 		<?php
			} else if (!$linked_author) {
				echo CHtml::openTag('div', array('class' => 'mb-10'));
				echo CHtml::link(
					'Link this user to an author',
					array('adminAuthor/prepareUserLink', 'user_id' => $model->id),
					array('class' => 'btn background-btn')
				);
				echo CHtml::closeTag('div');
			} else {
				echo CHtml::openTag('div', array('class' => 'mb-10'));
				$unlink_link =  CHtml::link(
					'Unlink author',
					array('AdminAuthor/unlinkUser', 'id' => $linked_author->id, 'user_id' => $model->id),
					array('class' => 'btn background-btn')
				);
				echo CHtml::closeTag('div');
			?>
 			<div class="alert alert-gigadb-info">
 				<div class="mb-10">
 					This user is linked to author: <? echo $linked_author->getDisplayName() ?> (<? echo $linked_author->id ?>)
 				</div>
 				<div class="btn-toolbar">
 					<? echo $unlink_link ?>
 				</div>
 			</div>
 	    <?php
			}
		}
		?>

 	<div>
 		<div class="subsection">
 			<div class="well">
 				<? $form = $this->beginWidget('CActiveForm', array(
						'id' => 'admin-user-form',
						'enableAjaxValidation' => false,
						'htmlOptions' => array('class' => 'form-horizontal create-user-form')
					)) ?>

 				<p class="mb-10" aria-hidden="true">Fields with <span class="symbol">*</span> are required.</p>

         <?php if ($model->hasErrors()) : ?>
            <div class="alert alert-danger">
                <?php echo $form->errorSummary($model); ?>
            </div>
        <?php endif; ?>

        <?php
          CHtml::$afterRequiredLabel = '<span aria-hidden="true"> *</span>';
        ?>

 				<?php
					$this->widget('application.components.controls.TextField', [
                        'form' => $form,
                        'model' => $model,
                        'labelOptions' => [
                            'class' => 'col-xs-3',
                        ],
                        'inputWrapperOptions' => 'col-xs-9',
                        'attributeName' => 'email',
                        'inputOptions' => [
							'required' => 'required',
						],
					]);
					$this->widget('application.components.controls.TextField', [
						'form' => $form,
						'model' => $model,
						'labelOptions' => [
							'class' => 'col-xs-3',
						],
						'inputWrapperOptions' => 'col-xs-9',
						'attributeName' => 'first_name',
						'inputOptions' => [
							'required' => 'required',
						],
					]);
					$this->widget('application.components.controls.TextField', [
						'form' => $form,
						'model' => $model,
						'labelOptions' => [
							'class' => 'col-xs-3',
						],
						'inputWrapperOptions' => 'col-xs-9',
						'attributeName' => 'last_name',
						'inputOptions' => [
							'required' => 'required',
						],
					]);

					?>

 					<div class="form-group">
 						<?= $form->labelEx($model, 'role', array('class' => 'col-xs-3 control-label')) ?>
 						<div class="col-xs-9">
 							<?= $form->dropDownList($model, 'role', array('user' => 'user', 'admin' => 'admin'), array('class' => 'form-control', 'aria-describedby' => $model->hasErrors('role') ? 'role-error' : '')) ?>
 							<div id="role-error"><?= $form->error($model, 'role', array('class' => 'control-error help-block')) ?></div>
 						</div>
 					</div>

 				<?php
					$this->widget('application.components.controls.TextField', [
						'form' => $form,
						'model' => $model,
						'labelOptions' => [
							'class' => 'col-xs-3',
						],
						'inputWrapperOptions' => 'col-xs-9',
						'attributeName' => 'affiliation',
						'inputOptions' => [
							'required' => 'required',
						],
					]);
					?>
 				<div class="form-group">
 					<?= $form->labelEx($model, 'preferred_link', array('class' => 'col-xs-3 control-label')) ?>
 					<div class="col-xs-9">
 						<?= CHtml::activeDropDownList($model, 'preferred_link', User::$linkouts, array('class' => 'form-control', 'aria-describedby' => $model->hasErrors('preferred_link') ? 'preferred_link-error' : '')) ?>
 						<div id="preferred_link-error"><?= $form->error($model, 'preferred_link', array('class' => 'control-error help-block')) ?></div>
 					</div>
 				</div>
 				<div class="form-group checkbox-horizontal">
 					<label class="col-xs-3 control-label" for="User_newsletter"><?= Yii::t('app', 'Mailing list') ?></label>
 					<div class="col-xs-9">
 						<?php echo $form->checkbox($model, 'newsletter', array('aria-describedby' => 'newsletter-desc')); ?>
 					</div>
 					<div class="col-xs-9" id="newsletter-desc">
 						<p>Please tick here to join the GigaDB mailing list to receive news, updates and quarterly newsletters about GigaDB</p>
 					</div>
 				</div>

                <div class='form-group checkbox-horizontal'>
                    <label class='col-xs-3 control-label'
                           for='User_is_activated'><?= Yii::t('app', 'Activate the user account') ?></label>
                    <div class="col-xs-9">
                        <?php echo $form->checkbox($model, 'is_activated', array('aria-describedby' => 'activation-desc')); ?>
                    </div>
                    <div class="col-xs-9" id="activation-desc">
                        <p>Please tick here to activate the user account</p>
                    </div>
                </div>

 				<hr>
 				<div class="pull-right">
 					<?= CHtml::submitButton($model->isNewRecord ? Yii::t('app', 'Register') : 'Save', array('class' => 'btn background-btn submit-btn')) ?>
 				</div>
 				<div class="clearfix"></div>
 				<? $this->endWidget() ?>
 			</div><!--well-->
 		</div>
 	</div><!-- user-form -->
 </div>
