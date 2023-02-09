<div class="row">
    <div class="span10 offset1 form well">
        <?php
        $form = $this->beginWidget(
            'CActiveForm',
            [
                'id'                   => 'file-form',
                'enableAjaxValidation' => false,
                'htmlOptions'          => ['class' => 'form-horizontal'],
            ]
        );
        ?>
        <p class="note">Fields with <span class="required">*</span> are required.</p>
        <?php echo $form->errorSummary($model); ?>
        <div class="control-group">
            <?php echo $form->labelEx($model, 'action', ['class' => 'control-label']); ?>
            <div class="controls">
                <?php
                $data = [
                    'Comment'                     => 'Comment',
                    'Curator assigned, ChrisA'    => 'Curator assigned, ChrisA',
                    'Curator assigned, MaryAnn'   => 'Curator assigned, MaryAnn',
                    'Curator assigned, Chris'     => 'Curator assigned, Chris',
                    'Curator assigned, Jesse'     => 'Curator assigned, Jesse',
                    'Status changed to Request'   => 'Status changed to Request',
                    'Status changed to Uploaded'  => 'Status changed to Uploaded',
                    'Status changed to Published' => 'Status changed to Published',
                ]
                ?>
                <?php echo $form->dropDownList($model, 'action', $data, ['class' => 'span4']); ?>
                <?php echo $form->error($model, 'action'); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo $form->labelEx($model, 'comments', ['class' => 'control-label']); ?>
            <div class="controls">
                <?php echo $form->textArea($model, 'comments', ['rows' => 6, 'cols' => 50]); ?>
                <?php echo $form->error($model, 'comments'); ?>
            </div>
        </div>
        <div class="pull-right">
            <a href="/adminDataset/update/id/<?php echo $dataset_id; ?>" class="btn">Cancel</a>
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', ['class' => 'btn']); ?>
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