<div class="container dataset-submission-page">

  <?php
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => 'Create Dataset',
    'breadcrumbItems' => []
  ]);

  ?>


  <?php
  $form = $this->beginWidget(
    'CActiveForm',
    array(
      'id' => 'dataset-form',
      'enableAjaxValidation' => false,
      'htmlOptions' => array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
      ),
    )
  );
  ?>

  <?
  $this->renderPartial('_nav', array('model' => $model));
  ?>

  <?
  $this->renderPartial('_form1', array('model' => $model, 'form' => $form, 'image' => $image));
  ?>


  <?php $this->endWidget(); ?>

</div>

<script>
  $('.js-submit').click(function (e) {
    e.preventDefault();
    $('#dataset-form').submit();
  });
</script>