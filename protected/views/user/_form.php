 <div class="content col-md-offset-2 col-md-8">

 	<?php
		foreach (Yii::app()->user->getFlashes() as $key => $message) {
			echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
		}
		?>
 	<?php
		$user_command = UserCommand::model()->findByAttributes(array("requester_id" => $model->id, "status" => "pending"));
		$linked_author = $model->id ? Author::findAttachedAuthorByUserId($model->id) : null;

		?>

 	<?php
		if (!$model->isNewRecord) {
			if (null != $user_command) {
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
			} else if (null ==  $linked_author) {
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
 			<?php
				$isAdmin = Yii::app()->user->checkAccess('manageUsers');
				if ($model->isNewRecord && !$isAdmin) { ?>
 				<p class="mb-10"><?= Yii::t('app', 'GigaScience appreciates your interest in the GigaDB project. With a GigaDB account, you can submit new datasets to the database. Also, GigaDB can automatically notify you of new content which matches your interests. Please fill out the following information and register to enjoy the benefits of GigaDB membership!') ?></p>
 			<? }
				?>
 			<?php Yii::app()->captcha->generate(); ?>
 			<div class="well">
 				<? $form = $this->beginWidget('CActiveForm', array(
						'id' => 'user-form',
						'enableAjaxValidation' => false,
						'htmlOptions' => array('class' => 'form-horizontal create-user-form')
					)) ?>

 				<p class="mb-10">Fields with <span class="symbol">*</span> are required.</p>

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
					$this->widget('application.components.controls.PasswordField', [
						'form' => $form,
						'model' => $model,
						'labelOptions' => [
							'class' => 'col-xs-3',
						],
						'inputWrapperOptions' => 'col-xs-9',
						'attributeName' => 'password',
						'inputOptions' => [
							'required' => 'required',
						],
					]);
					$this->widget('application.components.controls.PasswordField', [
						'form' => $form,
						'model' => $model,
						'labelOptions' => [
							'class' => 'col-xs-3',
						],
						'inputWrapperOptions' => 'col-xs-9',
						'attributeName' => 'password_repeat',
						'inputOptions' => [
							'required' => 'required',
						],
					]);
					?>


 				<? if (Yii::app()->user->checkAccess('admin')) { ?>
 					<div class="form-group">
 						<?= $form->labelEx($model, 'role', array('class' => 'col-xs-3 control-label')) ?>
 						<div class="col-xs-9">
 							<?= $form->dropDownList($model, 'role', array('user' => 'user', 'admin' => 'admin'), array('class' => 'form-control', 'aria-describedby' => $model->hasErrors('role') ? 'role-error' : '')) ?>
 							<div id="role-error"><?= $form->error($model, 'role', array('class' => 'control-error help-block')) ?></div>
 						</div>
 					</div>
 				<? } ?>
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
 				<div class="form-group checkbox-group">
 					<label class="col-xs-3 control-label" for="User_newsletter"><?= Yii::t('app', 'Mailing list') ?></label>
 					<div class="col-xs-9">
 						<?php echo $form->checkbox($model, 'newsletter', array('aria-describedby' => 'newsletter-desc')); ?>
 					</div>
 					<div class="col-xs-9" id="newsletter-desc">
 						<p>Please tick here to join the GigaDB mailing list to receive news, updates and quarterly newsletters about GigaDB</p>
 					</div>
 				</div>
 				<div class="form-group checkbox-group">
 					<?= $form->labelEx($model, 'terms', array('class' => 'col-xs-3 control-label')) ?>
 					<div class="col-xs-9">
 						<?php echo $form->checkbox($model, 'terms', array('aria-describedby' => $model->hasErrors('terms') ? 'terms-error terms-desc' : 'terms-desc')); ?>
 						<div id="terms-error"><?= $form->error($model, 'terms', array('class' => 'control-error help-block')) ?></div>
 						<p id="terms-desc">Please tick here to confirm you have read and understood our <a href="/site/term#policies">Terms of use</a> and <a href="/site/term#privacy">Privacy Policy</a></p>
 					</div>
 				</div>



 				<? if ($model->isNewRecord) { ?>
 					<div class="form-group">
 						<?php echo $form->labelEx($model, 'verifyCode', array('class' => 'col-xs-3 control-label')); ?>
 						<div class="col-xs-9">
 							<div style="width:100%">
 								<img style="width:200px;" src="<?php echo Yii::app()->captcha->output(); ?>" alt="Type the word in the image">
 							</div>
 							<br>
 							<br>
 							<?php echo $form->textField($model, 'verifyCode', array('class' => 'form-control', 'aria-describedby' => $model->hasErrors('verifyCode') ? 'verifyCode-error verifyCode-desc' : 'verifyCode-desc')); ?>
 							<div id="verifyCode-desc" class="hint control-description help-block">Please enter the letters as they are shown in the image above.
 								<br />Letters are case-sensitive.
 							</div>
 							<div id="verifyCode-error">
 								<?php echo $form->error($model, 'verifyCode', array('class' => 'control-error help-block')); ?>
 							</div>
 						</div>
 					</div>
 				<? } ?>
 				<hr>
 				<div class="pull-right">
 					<?= CHtml::submitButton($model->isNewRecord ? Yii::t('app', 'Register') : 'Save', array('class' => 'btn background-btn create-user-submit-btn')) ?>
 				</div>
 				<div class="clearfix"></div>
 				<? $this->endWidget() ?>
 			</div><!--well-->



 			<?php
				$path = "images/tempcaptcha/" . $text . ".png";
				$files = glob('images/tempcaptcha/*');
				foreach ($files as $file) {
					if (is_file($file))
						if ($file != $path)
							unlink($file);
				}
				?>
 		</div>
 	</div><!-- user-form -->
 </div>