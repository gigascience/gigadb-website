<div class="col-md-offset-3 col-md-6">
    <?php $form = $this->beginWidget('CActiveForm', array(
      'id' => 'file-form',
      'enableAjaxValidation' => false,
    )
    ); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php if ($model->hasErrors()) : ?>
        <div class="alert alert-danger">
          <?php echo $form->errorSummary($model); ?>
        </div>
    <?php endif;

    $this->widget('application.components.controls.DropdownField', [
      'form' => $form,
      'model' => $model,
      'attributeName' => 'dataset_id',
      'listDataOptions' => [
        'data' => Util::getDois(),
        'valueField' => 'id',
        'textField' => 'identifier',
      ],
      'inputOptions' => [
        'required' => true,
      ],
    ]);

    $this->widget('application.components.controls.TextField', [
      'form' => $form,
      'model' => $model,
      'attributeName' => 'action',
      'inputOptions' => [
        'maxlength' => 100
      ],
    ]);

    $this->widget('application.components.controls.TextArea', [
      'form' => $form,
      'model' => $model,
      'attributeName' => 'comments',
      'inputOptions' => [
          'rows' => 6,
          'cols' => 50
      ],
    ]);
    ?>

    <div class="pull-right btns-row">
      <a href="<?= $this->createAbsoluteUrl(
          'adminDataset/update',
          ['id' => $model->dataset_id],
      ) ?>" class="btn background-btn-o">Cancel</a>
      <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
    </div>

    <?php $this->endWidget(); ?>
  </div>

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