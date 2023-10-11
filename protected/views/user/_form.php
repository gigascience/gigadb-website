 <div class="content">

 	<?php
		foreach (Yii::app()->user->getFlashes() as $key => $message) {
			echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
		}
		?>
 	<?php
		$user_command = UserCommand::model()->findByAttributes(array("requester_id" => $model->id, "status" => "pending"));
		$linked_author = $model->id ? Author::findAttachedAuthorByUserId($model->id) : null;

		?>

 	<div class="clear"></div>
 	<?php
		if (!$model->isNewRecord) {
			if (null != $user_command) {
				$claimed_author = Author::model()->findByPk($user_command->actionable_id);
				$message = "This user has a pending claim on author " . $claimed_author->getDisplayName();
				$validate_link = CHtml::link(
					'Validate',
					array('AdminUserCommand/validate', 'id' => $user_command->id),
					array('class' => 'btn')
				);
				$reject_link = CHtml::link(
					'Reject',
					array('AdminUserCommand/reject', 'id' => $user_command->id),
					array('class' => 'btn')
				);
				$author_link = CHtml::link(
					'Author info',
					array('AdminAuthor/view', 'id' => $user_command->actionable_id),
					array('class' => 'btn')
				);
		?>
 			<div class="alert alert-info">
 				<? echo $message ?>
 				<div class="btn-toolbar">
 					<? echo $validate_link ?>
 					<? echo $reject_link ?>
 					<? echo $author_link ?>
 				</div>
 			</div>

 		<?php
			} else if (null ==  $linked_author) {
				echo CHtml::link(
					'Link this user to an author',
					array('adminAuthor/prepareUserLink', 'user_id' => $model->id),
					array('class' => 'btn')
				);
			} else {
				$unlink_link =  CHtml::link(
					'Unlink author',
					array('AdminAuthor/unlinkUser', 'id' => $linked_author->id, 'user_id' => $model->id),
					array('class' => 'btn')
				);
			?>
 			<div class="alert">This user is linked to author: <? echo $linked_author->getDisplayName() ?> (<? echo $linked_author->id ?>)
 				<div class="btn-toolbar">
 					<? echo $unlink_link ?>
 				</div>
 			</div>
 	<?php
			}
		}
		?>

 	<div class="container">
 		<div class="section page-title-section">
 			<div class="page-title">
 				<nav aria-label="breadcrumbs">
 					<ol class="breadcrumb pull-right">
 						<li><a href="/">Home</a></li>
 						<li class="active">Personal details</li>
 					</ol>
 				</nav>
 				<h4>Registration</h4>
 			</div>
 		</div>
 		<div class="subsection" style="margin-bottom: 130px;">

 			<div class="clear"></div>
 			<?php if ($model->isNewRecord) { ?>
 				<p><?= Yii::t('app', 'GigaScience appreciates your interest in the GigaDB project. With a GigaDB account, you can submit new datasets to the database. Also, GigaDB can automatically notify you of new content which matches your interests. Please fill out the following information and register to enjoy the benefits of GigaDB membership!') ?></p>
 			<? }
				?>
 			<?php Yii::app()->captcha->generate(); ?>
 			<p>Fields with <span class="symbol">*</span> are required.</p>
 			<div class="col-md-offset-2 col-md-8 well">
 				<? $form = $this->beginWidget('CActiveForm', array(
						'id' => 'user-form',
						'enableAjaxValidation' => false,
						'htmlOptions' => array('class' => 'form-horizontal create-user-form')
					)) ?>

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
 				<div class="form-group">
 					<label class="col-xs-3 control-label" for="User_newsletter"><?= Yii::t('app', 'Mailing list') ?></label>
 					<div class="col-xs-9">
 						<?php echo $form->checkbox($model, 'newsletter', array('aria-describedby' => 'newsletter-desc')); ?>
 					</div>
 					<div class="col-xs-9" id="newsletter-desc">
 						<p>Please tick here to join the GigaDB mailing list to receive news, updates and quarterly newsletters about GigaDB</p>
 					</div>
 				</div>
 				<div class="form-group">
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
 								<img style="width:200px;" src="<?php echo Yii::app()->captcha->output(); ?>">
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
 		</div><!--span8-->
 	</div><!-- user-form -->
 </div>