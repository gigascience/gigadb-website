<?
if (Yii::app()->user->hasFlash('saveSuccess'))
  echo Yii::app()->user->getFlash('saveSuccess');

$cs = Yii::app()->getClientScript();
$cssCoreUrl = $cs->getCoreScriptUrl();
$cs->registerCssFile($cssCoreUrl . '/jui/css/base/jquery-ui.css');
$cs->registerCssFile('/css/jquery.tag-editor.css');
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/caret/1.0.0/jquery.caret.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tag-editor/1.0.20/jquery.tag-editor.min.js"></script>

<div class="well col-xs-12">
  <div class="row">
    <div class="col-xs-12">
      <p class="note">Fields with <span class="required">*</span> are required.</p>

      <div role="alert">
        <?php if ($model->hasErrors()): ?>
          <div class="alert alert-danger">
            <?php echo $form->errorSummary($model); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- row 1 col 1 -->
    <div class="col-xs-5 form-block-1">
      <div class="form-group">
        <?php echo $form->labelEx($model, 'submitter_id', array('class' => 'control-label col-xs-4', 'for' => 'email')); ?>
        <div class="col-xs-8">
          <?php
          $email = Yii::app()->user->getEmail();
          echo CHtml::textField(
            "email",
            $email,
            array('size' => 60, 'maxlength' => 300, 'readonly' => "readonly", "class" => "form-control", 'required' => true, 'aria-required' => 'true', 'aria-describedby' => $model->hasErrors('submitter_id') ? 'submitter_id-error' : '')
          );
          ?>
          <div id="submitter_id-error" role="alert" class="control-error help-block">
            <?php echo $form->error($model, 'submitter_id'); ?>
          </div>
        </div>
      </div>

      <?php
      $this->widget('application.components.controls.TextField', [
        'form' => $form,
        'model' => $model,
        'attributeName' => 'title',
        'labelOptions' => ['class' => 'col-xs-4'],
        'inputWrapperOptions' => 'col-xs-8',
        'description' => 'This should be a short descriptive title of the dataset to be submitted',
        'inputOptions' => [
          'size' => 60,
          'maxlength' => 300,
          'required' => true
        ],
      ]);
      ?>


      <fieldset class="form-group" aria-label="Dataset size and unit">
        <label class="control-label col-xs-4" for="Dataset_dataset_size">Estimated Dataset Size</label>
        <div class="col-xs-8">
          <div class="row sibling-controls">
            <div class="col-xs-7 sibling-control">
              <?php echo $form->textField($model, 'dataset_size', array(
                'type' => 'number',
                'size' => 60,
                'maxlength' => 200,
                'class' => 'form-control',
                'required' => true,
                'aria-required' => 'true',
                'aria-describedby' => $model->hasErrors('name') ? 'dataset_size-error dataset_size-desc' : 'dataset_size-desc'
              )
              );
              ?>
            </div>
            <div class="col-xs-5 sibling-control">
              <?php
              echo CHtml::activeDropDownList($model, 'union', array('B' => 'Bytes', 'M' => 'MB', 'G' => 'GB', 'T' => 'TB'), array('class' => 'form-control', 'aria-label' => 'Unit of dataset size'));
              ?>
            </div>
          </div>
          <p id="dataset_size-desc" class="control-description help-block">The approximate combined size of all
            the files that
            you intend to submit</p>
          <div id="dataset_size-error" role="alert" class="control-error help-block">
            <?php echo $form->error($model, 'dataset_size'); ?>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Dataset Types</legend>
        <!-- checkboxes -->
        <div class="checkbox-group">
          <?php
          $datasetTypes = CHtml::listData(Type::model()->findAll(), 'id', 'name');
          $checkedStatuses = array_map(function ($id) use ($model) {
            return in_array($id, $model->types) ? 'checked="checked"' : '';
          }, array_keys($datasetTypes));

          foreach ($datasetTypes as $id => $datasetType): ?>
            <div class="checkbox-horizontal">
              <?php
              $checkedHtml = $checkedStatuses[$id];
              $checkboxId = "Dataset_$datasetType";
              echo $form->labelEx($model, "$datasetType", array('class' => 'col-md-4 control-label'));

              ?>

              <div class="col-md-8 col-xs-1">
                <input type="checkbox" id="<?php echo $checkboxId ?>" name="datasettypes[<?php echo $id ?>]"
                  value="<?php echo $id; ?>" <?php echo $checkedHtml; ?> />
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </fieldset>
    </div>

    <?php echo $form->hiddenField($image, 'location', array('size' => 60, 'maxlength' => 200, 'readonly' => "readonly", 'class' => 'image')); ?>

    <!-- row 1 col 2 -->
    <fieldset class="col-xs-offset-1 col-xs-5 form-block-2">
      <legend>Image</legend>

      <div class="form-group checkbox-horizontal">
        <label class="col-md-4 control-label" for="is_no_image">No image</label>
        <div class="col-md-8 col-xs-1">
          <?php echo $form->checkBox($image, 'is_no_image', array('id' => 'is_no_image', 'aria-describedby' => 'is_no_image-description', 'class' => 'js-no-image')); ?>
          <div id="is_no_image-description" class="control-description help-block">Check it if you don't want to upload
            an image</div>
        </div>
      </div>

      <div class="form-group js-image">
        <label class="control-label col-xs-4" for="Images_image_upload">Upload<span aria-hidden="true"> *</span></label>
        <div class="col-xs-8">
          <?php echo $form->fileField(
            $image,
            'image_upload',
            array(
              'class' => 'image form-control',
              'required' => true,
              'aria-required' => 'true',
              'aria-describedby' => $form->error($image, 'image_upload') ? 'Images_image_upload-error Images_image_upload-desc' : 'Images_image_upload-desc'
            )
          ); ?>
          <p id="Images_image_upload-desc" class="control-description help-block">Upload an image from your local
            computer/network</p>
          <div id="Images_image_upload-error" class="control-error help-block" role="alert">
            <?php echo $form->error($image, 'image_upload'); ?>
          </div>
        </div>
      </div>

      <?php
      $this->widget('application.components.controls.TextField', [
        'form' => $form,
        'model' => $image,
        'attributeName' => 'source',
        'description' => 'From where did you get the image, e.g. wikipedia',
        'groupOptions' => ['class' => 'js-image'],
        'inputOptions' => [
          'size' => 60,
          'maxlength' => 200,
          'required' => true,
        ],
        'labelOptions' => ['class' => 'col-xs-4'],
        'inputWrapperOptions' => 'col-xs-8',
      ]);

      $this->widget('application.components.controls.TextField', [
        'form' => $form,
        'model' => $image,
        'attributeName' => 'tag',
        'description' => 'A brief descriptive title of the image, this will be shown to users if they hover over the image',
        'groupOptions' => ['class' => 'js-image'],
        'inputOptions' => [
          'size' => 60,
          'maxlength' => 200,
        ],
        'labelOptions' => ['class' => 'col-xs-4'],
        'inputWrapperOptions' => 'col-xs-8',
      ]);
      ?>

      <div class="form-group js-image">
        <?php echo $form->labelEx($image, 'license', array('class' => 'control-label col-xs-4')); ?>
        <div class="col-xs-8">
          <?php echo $form->textField($image, 'license', array('size' => 60, 'maxlength' => 300, 'class' => 'form-control', 'required' => true, 'aria-required' => 'true', 'aria-describedby' => $form->error($image, 'license') ? 'Images_license-error Images_license-desc' : 'Images_license-desc')); ?>
          <p id="Images_license-desc" class="control-description help-block">GigaScience database will
            only use images that are free for others to re-use,
            primarily this is <a target='_blank' href='http://creativecommons.org/about/cc0'>Creative Commons 0 license
              (CC0)</a></p>
          <div id="Images_license-error" role="alert" class="control-error help-block">
            <?php echo $form->error($image, 'license'); ?>
          </div>
        </div>
      </div>


      <?php

      $this->widget('application.components.controls.TextField', [
        'form' => $form,
        'model' => $image,
        'attributeName' => 'photographer',
        'description' => 'The person(s) that should be credited for the image',
        'groupOptions' => ['class' => 'js-image'],
        'inputOptions' => [
          'size' => 60,
          'maxlength' => 200,
          'required' => true,
        ],
        'labelOptions' => ['class' => 'col-xs-4'],
        'inputWrapperOptions' => 'col-xs-8',
      ]);
      ?>
    </fieldset>

  </div>

  <hr />

  <!-- row 2 -->
  <div class="row form-block-3 full-width-block">

    <div class="col-xs-12">
      <?php

      $this->widget('application.components.controls.TextArea', [
        'form' => $form,
        'model' => $model,
        'attributeName' => 'description',
        'description' => 'Please provide a full description of the datatset, this may look like an article abstract giving a brief background of the research and a
        description of the results to be found in the dataset
        (it should be between 100 and 500 word in length).
        Please note this text box accepts HTML code tags for formatting,
        so you may use &quot;&lt; br &gt;&quot; for line breaks, &quot;&lt; em &gt;&QUOT; <em>for italics</em> &quot;
        &lt; em /&gt;&quot;
        and &quot;&lt; b &gt;&quot; <b>for bold</b> &quot;&lt; b/ &gt;&quot;',
        'labelOptions' => ['class' => 'col-xs-4'],
        'inputWrapperOptions' => 'input-wrapper col-xs-6',
        'inputOptions' => [
          'rows' => 6,
          'cols' => 100
        ],
      ]);

      ?>
      <div class="form-group">
        <?php echo CHtml::label('Keywords', 'keywords', array('class' => 'control-label col-xs-4')); ?>
        <div class="col-xs-6 input-wrapper">
          <?php echo CHtml::textArea('keywords', '', array('class' => 'form-control', 'size' => 60, 'maxlength' => 300)); ?>
        </div>
      </div>
    </div>
  </div>

</div>

<div class="col-xs-12 form-control-btns">
  <a href="<?php echo Yii::app()->createUrl('/user/view_profile') ?>" class="btn background-btn-o">Cancel</a>
  <?php echo CHtml::submitButton('Next', array('class' => 'btn background-btn submit-btn', 'id' => 'next-btn')); ?>
</div>


<script>
  $(".next1").click(function () {
    $("#next-btn").click();
  });
</script>
<script>
  <?php
  $js_array = json_encode($model->getSemanticKeywords());
  echo "var existingTags = " . $js_array . ";\n";
  ?>
  $('#keywords').tagEditor({
    initialTags:
      existingTags,
    delimiter: ',', /* comma */
    placeholder: 'Enter keywords (separated by commas) ...'
  });

  const MARKED_REQUIRED = 'js-marked-required';
  const IMAGE_GROUP_SELECTOR = '.js-image';

  function handleNoImage(el) {
    const isChecked = $(el).is(':checked');

    $(IMAGE_GROUP_SELECTOR).each(function () {
      const group = $(this);
      const input = $(this).find('input:not([type="hidden"])');

      group.addClass('disabled');
      input.attr('aria-disabled', isChecked);

      if (group.hasClass(MARKED_REQUIRED)) {
        input.attr('aria-required', !isChecked);
        input.attr('required', !isChecked);
      }
    });
  }

  $(document).ready(function () {
    $(IMAGE_GROUP_SELECTOR).each(function () {
      const group = $(this);
      const input = $(this).find('input:not([type="hidden"])');

      if (input.prop('required')) {
        group.addClass(MARKED_REQUIRED);
      }
    });

    const noImageCheckbox = $('.js-no-image');

    handleNoImage(noImageCheckbox);

    noImageCheckbox.change(function () {
      handleNoImage(this);
    });
  })
</script>