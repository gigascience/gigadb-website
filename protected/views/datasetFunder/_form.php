<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="section form row">

  <div class="col-md-offset-3 col-md-6">
    <?php $form = $this->beginWidget(
      'CActiveForm',
      array(
        'id' => 'dataset-funder-form',
        'enableAjaxValidation' => false,
      )
    ); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php if ($model->hasErrors()): ?>
      <div class="alert alert-danger">
        <?php echo $form->errorSummary($model); ?>
      </div>
    <?php endif; ?>

    <?php
    $this->widget('application.components.controls.DropdownField', [
      'form' => $form,
      'model' => $model,
      'attributeName' => 'dataset_id',
      'dataset' => $datasets,
      'inputOptions' => [
        'required' => true,
      ],
    ]);
    $this->widget('application.components.controls.DropdownField', [
      'form' => $form,
      'model' => $model,
      'attributeName' => 'funder_id',
      'dataset' => $funders,
      'inputOptions' => [
        'required' => true,
        'class' => 'select2-combobox js-select2-combobox'
      ],
    ]);
    $this->widget('application.components.controls.TextArea', [
      'form' => $form,
      'model' => $model,
      'attributeName' => 'grant_award',
      'inputOptions' => [
        'rows' => 6,
        'cols' => 50
      ],
    ]);
    $this->widget('application.components.controls.TextArea', [
      'form' => $form,
      'model' => $model,
      'attributeName' => 'awardee',
      'inputOptions' => [
        'rows' => 6,
        'cols' => 50
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
      <a href="/datasetFunder/admin" class="btn background-btn-o">Cancel</a>
      <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
    </div>

    <?php $this->endWidget(); ?>
  </div>

</div>


<script>
  $(document).ready(function () {
    $('.js-select2-combobox').select2({
      placeholder: "Select an option",
      allowClear: true,
      dropdownCssClass: 'select2-dropdown-override',
      selectionCssClass: 'select2-selection-override',
      width: '100%'
    }).on('select2:open', function () {
      // search input does not get automatic focus
      const searchInput = document.querySelector('.select2-search__field')
      searchInput.focus();
    });
  });
</script>