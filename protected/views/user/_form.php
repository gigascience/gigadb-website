 <div class="content">

<?php
    foreach(Yii::app()->user->getFlashes() as $key => $message) {
        echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
    }
?>
<?php
      $user_command = UserCommand::model()->findByAttributes(array("requester_id" => $model->id, "status" => "pending")) ;
      $linked_author = Author::findAttachedAuthorByUserId($model->id) ;

?>

<div class="clear"></div>
<?php
      if ( null != $user_command ) {
      	$claimed_author = Author::model()->findByPk($user_command->actionable_id);
      	$message = "This user has a pending claim on author ". $claimed_author->getDisplayName() ;
      	$validate_link = CHtml::link('Validate', 
                                    array('AdminUserCommand/validate', 'id' => $user_command->id),
                                    array('class' => 'btn'));
      	$reject_link = CHtml::link('Reject', 
                                    array('AdminUserCommand/reject', 'id' => $user_command->id),
                                    array('class' => 'btn'));
      	$author_link = CHtml::link('Author info', 
                                    array('AdminAuthor/view', 'id' => $user_command->actionable_id),
                                    array('class' => 'btn'));
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
      }
      else if ( null ==  $linked_author) {
          echo CHtml::link('Link this user to an author', 
                                    array('adminAuthor/prepareUserLink', 'user_id'=>$model->id),
                                    array('class' => 'btn')); 
      }else {
      	$unlink_link =  CHtml::link('Unlink author', 
                                    array('AdminAuthor/unlinkUser', 'id' => $linked_author->id, 'user_id'=>$model->id),
                                    array('class' => 'btn'));
?>
        <div class="alert">This user is linked to author: <? echo $linked_author->getDisplayName() ?> (<? echo $linked_author->id ?>)
			<div class="btn-toolbar">
			    <? echo $unlink_link ?>
			</div>
        </div>
<?php
      }
?>
 	
        <div class="container">
              <section class="page-title-section">
                <div class="page-title">
                    <ol class="breadcrumb pull-right">
                        <li><a href="/">Home</a></li>
                        <li class="active">Personal details</li>
                    </ol>
                    <h4>Registration</h4>
                </div>
            </section>
        <div class="subsection" style="margin-bottom: 130px;">
	
		<div class="clear"></div>
		<?php if ($model->isNewRecord) {?>
		<p><?=Yii::t('app' , 'GigaScience appreciates your interest in the GigaDB project. With a GigaDB account, you can submit new datasets to the database. Also, GigaDB can automatically notify you of new content which matches your interests. Please fill out the following information and register to enjoy the benefits of GigaDB membership!')?></p>
<?}
		?>
		<?php $text = $this->captchaGenerator(); ?>
		<p>Fields with <span class="symbol">*</span> are required.</p>
		<div class="create-div">
			<? $form=$this->beginWidget('CActiveForm', array(
				'id'=>'user-form',
				'enableAjaxValidation'=>false,
				'htmlOptions'=>array('class'=>'form-horizontal')
			)) ?>
				 <div class="form-group">
					<?= $form->labelEx($model,'email', array('class'=>'col-xs-3 control-label')) ?>
					<div class="col-xs-9">
						<?= $form->textField($model,'email',array('class'=>'form-control')) ?>
						<font color="red"><?= $form->error($model,'email') ?></font>
					</div>
				</div>

				<div class="form-group">
					<?= $form->labelEx($model,'first_name', array('class'=>'col-xs-3 control-label')) ?>
					<div class="col-xs-9">
						<?= $form->textField($model,'first_name',array('class'=>'form-control')) ?>
						<font color="red"><?= $form->error($model,'first_name') ?></font>
					</div>
				</div>

				<div class="form-group">
					<?= $form->labelEx($model,'last_name', array('class'=>'col-xs-3 control-label')) ?>
					<div class="col-xs-9">
						<?= $form->textField($model,'last_name',array('class'=>'form-control')) ?>
						<font color="red"><?= $form->error($model,'last_name') ?></font>
					</div>
				</div>

				<div class="form-group">
					<?= $form->labelEx($model,'password', array('class'=>'col-xs-3 control-label')) ?>
					<div class="col-xs-9">
						<?= $form->passwordField($model,'password',array('class'=>'form-control')) ?>
						 <font color="red"><?= $form->error($model,'password') ?></font>
					</div>
				</div>

				<div class="form-group">
					<?= $form->labelEx($model,'password_repeat', array('class'=>'col-xs-3 control-label')) ?>
					<div class="col-xs-9">
						<?= $form->passwordField($model,'password_repeat',array('class'=>'form-control')) ?>
						<font color="red"><?= $form->error($model,'password_repeat') ?></font>
					</div>
				</div>
				<? if (Yii::app()->user->checkAccess('admin')) { ?>
					<div class="form-group">
						<?= $form->labelEx($model,'role', array('class'=>'col-xs-3 control-label')) ?>
						<div class="col-xs-9">
							<?= $form->dropDownList($model,'role',array('user'=>'user','admin'=> 'admin','class'=>'dropdown-menu')) ?>
							 <font color="red"><?= $form->error($model,'role') ?></font>
						</div>
					</div>
				<? } ?>
				<div class="form-group">
					<?= $form->labelEx($model,'affiliation', array('class'=>'col-xs-3 control-label')) ?>
					<div class="col-xs-9">
						<?= $form->textField($model,'affiliation',array('class'=>'form-control')) ?>
						<font color="red"><?= $form->error($model,'affiliation') ?></font>
					</div>
				</div>
				<div class="form-group">
					<?= $form->labelEx($model,'preferred_link', array('class'=>'col-xs-3 control-label')) ?>
					<div class="col-xs-9">
						<?= CHtml::activeDropDownList($model,'preferred_link', User::$linkouts, array()) ?>
						<font color="red"><?= $form->error($model,'preferred_link') ?></font>
					</div>
				</div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?=Yii::t('app' , 'Mailing list')?></label>
                                   
				    <div class="col-xs-9">				    	
                                         <?php echo $form->checkbox($model,'newsletter'); ?>
				    </div>
                                    <div class="col-xs-9">
                                        <p>Please tick here to join the GigaDB mailing list to receive news, updates and quarterly newsletters about GigaDB</p>   
                                    </div>
                                 </div>
                                <div class="form-group">
                                    <?= $form->labelEx($model,'terms', array('class'=>'col-xs-3 control-label')) ?>              
				    <div class="col-xs-9">				    	
                                         <?php echo $form->checkbox($model,'terms'); ?>
                                         <font color="red"><?= $form->error($model,'terms') ?></font>
				    </div>
                                    <div class="col-xs-9">
                                     <p>Please tick here to confirm you have read and understood our <a href="/site/term#policies">Terms of use</a> and <a href="/site/term#privacy">Privacy Policy</a></p>
                                    </div>
                                 </div>
                                


			<? if ($model->isNewRecord) { ?>
			<div class="form-group">		
					<?php echo $form->labelEx($model,'verifyCode', array('class'=>'col-xs-3 control-label')); ?>		
			        <div class="col-xs-9">				
						<div style="width:100%">	
							<img style="width:200px;" src="/images/tempcaptcha/<?php echo $text; ?>.png">	
						</div>
                                    <br>
                                    <br>
						<?php echo $form->textField($model,'verifyCode',array('class'=>'form-control')); ?>	
						<div class="hint">Please enter the letters as they are shown in the image above.
						<br/>Letters are case-sensitive.</div>
						<?php echo $form->error($model, 'verifyCode'); ?>					
						</div>		
			    </div>
			<? } ?>
                        <hr>
                            <div class="button-div">
                                <?= MyHtml::submitButton($model->isNewRecord ? Yii::t('app' , 'Register') : 'Save', array('class'=>'btn background-btn')) ?>
                            </div>
                        <? $this->endWidget() ?>
		</div><!--well-->
		


	<?php 
		$path = "images/tempcaptcha/".$text.".png";
		$files = glob('images/tempcaptcha/*');
		foreach($files as $file){ 
		  if (is_file($file))
		  	if ($file != $path)
		  		 unlink($file); 
		}
	?>	
	</div><!--span8-->
    </div><!-- user-form -->
</div>

