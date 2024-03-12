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
        <?php echo $form->labelEx($model, 'submitter_id', array('class' => 'control-label col-xs-4')); ?>
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
        <?php echo CHtml::label('Estimated Dataset Size', '', array('class' => 'control-label col-xs-4', 'for' => 'Dataset_dataset_size'));
        ?>
        <div class="col-xs-8">
          <div class="row sibling-controls">
            <div class="col-xs-7 sibling-control">
              <?php echo $form->textField($model, 'dataset_size', array('type' => 'number', 'size' => 60, 'maxlength' => 200, 'class' => 'form-control', 'aria-describedby' => $model->hasErrors('name') ? 'dataset_size-error dataset_size-desc' : 'dataset_size-desc'));
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
    <div class="col-xs-offset-1 col-xs-5 form-block-2">
      <div class="control-group">
        <font class="control-label">No image</font>
        <a class="myHint" data-content="check it if you don't want to upload an image"></a>
        <div class="controls">
          <?php echo $form->checkBox($image, 'is_no_image', array('id' => 'image-upload')); ?>
          <!--input id="image-upload" type="checkbox" name="Images[is_no_image]"
                           style="margin-right:5px"/-->
        </div>
      </div>

      <div class="control-group">
        <label class="control-label">Image Upload</label>
        <a class="myHint" data-content="upload an image from your local computer/network"></a>
        <div class="controls">
          <?php echo $form->fileField($image, 'image_upload', array('class' => 'image')); ?>
          <?php echo $form->error($image, 'image_upload'); ?>
        </div>
      </div>

      <div class="control-group">
        <?php echo $form->labelEx($image, 'source', array('class' => 'control-label')); ?>
        <a class="myHint" data-content="from where did you get the image, e.g. wikipedia"></a>
        <div class="controls">
          <?php echo $form->textField($image, 'source', array('size' => 60, 'maxlength' => 200, 'class' => 'image')); ?>
          <?php echo $form->error($image, 'source'); ?>
        </div>
      </div>

      <div class="control-group">
        <?php echo $form->labelEx($image, 'tag', array('class' => 'control-label')); ?>
        <a class="myHint" data-content="A brief descriptive title of the image,
                   this will be shown to users if they hover over the image."></a>
        <div class="controls">
          <?php echo $form->textField($image, 'tag', array('size' => 60, 'maxlength' => 200, 'class' => 'image')); ?>
          <?php echo $form->error($image, 'tag'); ?>
        </div>
      </div>

      <div class="control-group">
        <?php echo $form->labelEx($image, 'license', array('class' => 'control-label')); ?>
        <a class="myHint" data-content="GigaScience database will
                   only use images that are free for others to re-use,
                   primarily this is Creative Commons 0 license (CC0)
                   please see <a target='_blank' href='http://creativecommons.org/about/cc0'>here</a>
                   for further reading on creative commons licenses."></a>
        <div class="controls">
          <?php echo $form->textField($image, 'license', array('size' => 60, 'maxlength' => 200, 'class' => 'image')); ?>
          <?php echo $form->error($image, 'license'); ?>
        </div>
      </div>

      <div class="control-group">
        <?php echo $form->labelEx($image, 'photographer', array('class' => 'control-label')); ?>
        <a class="myHint" data-content="The person(s) that should
                   be credited for the image"></a>
        <div class="controls">
          <?php echo $form->textField($image, 'photographer', array('size' => 60, 'maxlength' => 200, 'class' => 'image')); ?>
          <?php echo $form->error($image, 'photographer'); ?>
        </div>
      </div>
    </div>


    <!-- row 2 -->
    <div class="">
      <div class="control-group">
        <?php echo $form->labelEx($model, 'description', array('class' => 'control-label')); ?>
        <a class="myHint" data-content="Please provide a full description of the datatset, this may
                   look like an article abstract giving a brief background of the research and a
                   description of the results to be found in the dataset
                   (it should be between 100 and 500 word in length).
                   Please note this text box accepts HTML code tags for formatting,
                   so you may use &quot;&lt; br &gt;&quot; for line breaks, &quot;&lt; em &gt;&QUOT; <em>for italics</em> &quot;
                   &lt; em /&gt;&quot;
                   and &quot;&lt; b &gt;&quot; <b>for bold</b> &quot;&lt; b/ &gt;&quot;"></a>
        <div class="controls">
          <?php echo $form->textArea($model, 'description', array('rows' => 6, 'cols' => 100, 'style' => 'resize:vertical;width:610px')); ?>
          <?php echo $form->error($model, 'description'); ?>
        </div>
      </div>
    </div>
    <div class="">
      <div class="control-group">
        <?php echo CHtml::label('Keywords', 'keywords', array('class' => 'control-label')); ?>
        <div class="controls">
          <?php echo CHtml::textField('keywords', '', array('class' => '', 'size' => 60, 'maxlength' => 300)); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="">
    <a href="<?php echo Yii::app()->createUrl('/user/view_profile') ?>" class="btn">Cancel</a>
    <?php echo CHtml::submitButton('Next', array('class' => 'btn-green', 'id' => 'next-btn')); ?>
  </div>

</div>

<script>
  $('.date').datepicker();


  $(".next1").click(function () {
    $("#next-btn").click();
  });

  $(".myHint").popover();

  $(".myHintLink").popover({ trigger: 'manual' }).hover(function (e) {
    $(this).popover('show');
    e.preventDefault();
  });


  $('.myHintLink').on('mouseleave', function () {
    var v = $(this);
    setTimeout(
      function () {
        v.popover('hide');
      }, 2000);
  });

  $(function () {
    $('#image-upload').click(function () {
      if ($(this).is(':checked')) {
        $('.image').attr('disabled', true);
      } else {
        $('.image').attr('disabled', false);
      }
    });
  });

  function disableImage() {
    //        alert('here');
    if ($('#image-upload').is(':checked')) {
      $('.image').attr('disabled', true);
    }
  }

  window.onload = disableImage;

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
</script>