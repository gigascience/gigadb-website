
<h1>Link Temp File Folder</h1>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= MyHtml::link('Manage Files', array('admin')) ?>]
</div>
<? } ?>
<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
		<?  Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-1.8.21.custom.min.js'); ?>
		<div class="form">
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'file-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'dataset_id',array('class'=>'control-label')); ?>
				<div class="controls">
        <?= CHtml::activeDropDownList($model,'dataset_id',CHtml::listData(Dataset::model()->findAll("1=1 order by identifier desc"),'id','identifier')); ?>
		<?php echo $form->error($model,'dataset_id'); ?>
				</div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'folder_name',array('class'=>'control-label')); ?>
				
            <a class="myHint" data-content="input the detailed ftp address, for example<br/>
               aspera.gigadb.org"></a>                  
                      <div class="controls">              
		<?php echo $form->textField($model,'folder_name',array('size'=>60,'maxlength'=>100)); ?>
                      
		<?php echo $form->error($model,'folder_name'); ?>
				</div>
	</div>
        
        <div class="control-group">
		<?php echo $form->labelEx($model,'username',array('class'=>'control-label')); ?>
	 <div class="controls">              
		
                 <?php echo $form->textField($model,'username',array('size'=>60,'maxlength'=>100)); ?>   
                   
		<?php echo $form->error($model,'username'); ?>
         </div>
        </div>
        
               <div class="control-group">
		<?php echo $form->labelEx($model,'password',array('class'=>'control-label')); ?>
	 <div class="controls">              
		
                 <?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>100)); ?>   
                   
		<?php echo $form->error($model,'password'); ?>
         </div>
        </div>

	<div class="pull-right">
        <a href="/adminFile/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton('Link',array('class'=>'btn')); ?>
	</div>
 <?php $this->endWidget(); ?>

    
</div><!-- form -->
	</div>
</div>

    
<script>
$('.date').datepicker();
 $(".myHint").popover();
</script>
