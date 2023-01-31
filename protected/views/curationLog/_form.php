<div class="row">
    <div class="span10 offset1 form well">
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
                <?= CHtml::activeDropDownList($model,'dataset_id',CHtml::listData(Util::getDois(),'id','identifier')); ?>
                    <?php echo $form->error($model,'dataset_id'); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo $form->labelEx($model,'action',array('class'=>'control-label')); ?>
            <div class="controls">
                <?php echo $form->textField($model,'action',array('size'=>20,'maxlength'=>100)); ?>
                <?php echo $form->error($model,'action'); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo $form->labelEx($model,'comments',array('class'=>'control-label')); ?>
            <div class="controls">
                <?php echo $form->textArea($model,'comments',array('rows'=>6, 'cols'=>50)); ?>
                <?php echo $form->error($model,'comments'); ?>
            </div>
        </div>
        <div class="pull-right">
            <a href="/curationLog/admin" class="btn">Cancel</a>
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
        </div>
        <?php $this->endWidget(); ?>
    </div>
    <!-- form -->
</div>
<script type="text/javascript">
$('.date').datepicker({ 'dateFormat': 'yy-mm-dd' });
$('.btn-attr').click(function(e) {
    e.preventDefault();
    $('.js-new-attr').toggle();
})
$('.js-edit').click(function(e) {
    e.preventDefault();
    id = $(this).attr('data');

    row = $('.row-edit-' + id);
    if (id) {
        $.post('/adminFile/editAttr', { 'id': id }, function(result) {
            if (result.success) {
                row.html(result.data);
                //$('.js-new-attr').remove();
            }
        }, 'json');
    }
})
</script>