<?
$this->pageTitle = 'GigaDB - My GigaDB Page';
?>
    <? if (Yii::app()->user->hasFlash('keyword')) { ?>
                                                        <font color="green">
                                                            <div>
                                                                <?= Yii::app()->user->getFlash('keyword'); ?>
                                                            </div>
                                                        </font>
        <? } ?>
    <? if (Yii::app()->user->hasFlash('error')) { ?>
                                                            <div class="alert alert-danger" role="alert">
                                                                <?= Yii::app()->user->getFlash('error'); ?>
                                                            </div>
        <? } ?>
    <? if (Yii::app()->user->hasFlash('fileUpload')) { ?>
                                                             <div class="alert alert-success" role="alert">
                                                                <?= Yii::app()->user->getFlash('fileUpload'); ?>
                                                            </div>
        <? } ?>
    <? if (Yii::app()->user->hasFlash('uploadDeleted')) { ?>
                                                             <div class="alert alert-success" role="alert">
                                                                <?= Yii::app()->user->getFlash('uploadDeleted'); ?>
                                                            </div>
        <? } ?>
                    <div class="content">
                        <div class="container">
                          <?php
                          $this->widget('TitleBreadcrumb', [
                            'pageTitle' => 'Your profile page',
                            'breadcrumbItems' => [
                              ['label' => 'Home', 'href' => '/'],
                              ['isActive' => true, 'label' => 'Your profile'],
                            ]
                          ]);
                          ?>
                            <section>
                                <div style="padding-top: 1px;">
                                    <ul class="nav nav-tabs nav-border-tabs" role="tablist">
                                        <li role="presentation" class="active"><a href="#edit" aria-controls="edit" role="tab" data-toggle="tab">Personal details</a></li>
                                        <li role="presentation"><a href="#submitted" aria-controls="submitted" role="tab" data-toggle="tab">Your Uploaded Datasets</a></li>
                                        <li role="presentation"><a href="#authored" aria-controls="authored" role="tab" data-toggle="tab">Your Authored Datasets</a></li>
                                        <li role="presentation"><a href="#saved" aria-controls="saved" role="tab" data-toggle="tab">Your Saved Search</a></li>
                                    </ul>
                                </div>
                            </section>
                            <section>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="edit">
                                        <div class="row">
                                            <div class="col-xs-8 col-xs-offset-2">
                                                <div class="form well user-profile-box">
                                                    <div class="js-readonly-data">
                                                      <ul class="list-unstyled readonly-profile">
                                                      <?php
                                                      foreach ($model as $key => $value) {
                                                        if (strpos($key, '_') !== 0 && $key !== 'user_id') {
                                                          $label = $model->getAttributeLabel($key);

                                                          if (is_bool($value)) {
                                                            $value = $value ? 'Yes' : 'No';
                                                          }
                                                          ?>
                                                          <li class="row">
                                                            <div class="col-xs-5 readonly-profile-key"><?= $label ?></div>
                                                            <div class="col-xs-7 readonly-profile-value"><?= $value ?></div>
                                                          </li>
                                                          <?php
                                                        }
                                                      }
                                                      ?>
                                                      </ul>
                                                    </div>
                                                    <div class="js-editable-data">
                                                      <?php
                                                      $form = $this->beginWidget(
                                                        'CActiveForm',
                                                        array(
                                                          'id' => 'EditProfile-form',
                                                          'enableAjaxValidation' => false,
                                                          'htmlOptions' => array('class' => 'form-horizontal'),
                                                        )
                                                        );

                                                      $this->widget('application.components.controls.TextField', [
                                                        'form' => $form,
                                                        'model' => $model,
                                                        'attributeName' => 'email',
                                                        'inputOptions' => [
                                                          'class' => 'js-toggle-editable',
                                                          'required' => true,
                                                        ],
                                                        'labelOptions' => ['class' => 'col-xs-5'],
                                                        'inputWrapperOptions' => 'col-xs-7'
                                                      ]);
                                                      $this->widget('application.components.controls.TextField', [
                                                        'form' => $form,
                                                        'model' => $model,
                                                        'attributeName' => 'first_name',
                                                        'inputOptions' => [
                                                          'class' => 'js-toggle-editable',
                                                          'maxlength' => 60,
                                                          'required' => true,
                                                        ],
                                                        'labelOptions' => ['class' => 'col-xs-5'],
                                                        'inputWrapperOptions' => 'col-xs-7'
                                                      ]);
                                                      $this->widget('application.components.controls.TextField', [
                                                        'form' => $form,
                                                        'model' => $model,
                                                        'attributeName' => 'last_name',
                                                        'inputOptions' => [
                                                          'class' => 'js-toggle-editable',
                                                          'maxlength' => 60,
                                                          'required' => true,
                                                        ],
                                                        'labelOptions' => ['class' => 'col-xs-5'],
                                                        'inputWrapperOptions' => 'col-xs-7'
                                                      ]);
                                                      $this->widget('application.components.controls.TextField', [
                                                        'form' => $form,
                                                        'model' => $model,
                                                        'attributeName' => 'affiliation',
                                                        'inputOptions' => [
                                                          'class' => 'js-toggle-editable',
                                                          'maxlength' => 60,
                                                          'required' => true,
                                                        ],
                                                        'labelOptions' => ['class' => 'col-xs-5'],
                                                        'inputWrapperOptions' => 'col-xs-7'
                                                      ]);
                                                      ?>
                                                      <?php
                                                      $this->widget('application.components.controls.DropdownField', [
                                                        'form' => $form,
                                                        'model' => $model,
                                                        'attributeName' => 'preferred_link',
                                                        'dataset' => User::$linkouts,
                                                        'inputOptions' => [
                                                          'class' => 'js-toggle-editable',
                                                        ],
                                                        'labelOptions' => ['class' => 'col-xs-5'],
                                                        'inputWrapperOptions' => 'col-xs-7'
                                                      ]);
                                                      ?>
                                                      <div class="form-group checkbox-horizontal <?= $model->hasErrors('newsletter') ? 'has-error' : '' ?>">
                                                        <?= $form->label($model, 'newsletter', array('class' => 'col-xs-5 control-label')) ?>
                                                        <div class="col-xs-7">
                                                          <?php echo $form->checkbox($model, 'newsletter', array('aria-describedby' => 'newsletterError')); ?>
                                                        </div>
                                                        <div class="col-xs-7" id="newsletterError" role="alert">
                                                          <?php echo $form->error($model, 'newsletter', array('class' => 'control-error help-block')); ?>
                                                        </div>
                                                      </div>

                                                      <div class="controls btns-row btns-row-end">
                                                          <button id="cancel-btn" type="button" class="btn background-btn-o">
                                                              <?= Yii::t('app', 'Cancel') ?>
                                                          </button>
                                                          <?= CHtml::submitButton(Yii::t('app', 'Save'), array('id' => 'save-btn', 'class' => 'btn background-btn m-0')) ?>
                                                      </div>
                                                    </div>
                                                    <? $this->endWidget() ?>
                                                    </div>

                                              <div>
                                                <div class="btns-row pull-right">
                                                  <button id="edit-btn" type="button" class="btn background-btn">Edit Profile</button>
                                                  <a href="/user/changePassword" class="btn background-btn">
                                                      <?= Yii::t('app', 'Change Password') ?>
                                                  </a>
                                                  <a href="/datasetSubmission/upload" class="btn background-btn">
                                                      <?= Yii::t('app', 'Submit new dataset') ?>
                                                  </a>
                                                </div>
                                              </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="submitted">
                                        <?= $this->renderPartial('uploadedDatasets', array('uploadedDatasets' => $uploadedDatasets)); ?>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="authored">
                                        <?= $this->renderPartial('authoredDatasets', array('authoredDatasets' => $authoredDatasets, 'linkedAuthors' => $linkedAuthors)); ?>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="saved">
                                        <?= $this->renderPartial('searches', array('searchRecord' => $searchRecord)); ?>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <script>
                    document.addEventListener("DOMContentLoaded", function(e) { //This event is fired after deferred scripts are loaded
                        $('.js-editable-data').hide();

                        $('#edit-btn').on('click', function (e) {
                            $('#edit-btn').hide();
                            $('.js-readonly-data').hide();
                            $('.js-editable-data').show();
                            $('#EditProfile-form').find('input').first().focus();
                        });
                        $('#cancel-btn').on('click', function (e) {
                            $('#edit-btn').show();
                            $('.js-readonly-data').show();
                            $('.js-editable-data').hide();
                        });

                    });
                    </script>
                    <script>
                    document.addEventListener("DOMContentLoaded", function(event) { //This event is fired after deferred scripts are loaded

                        $(".hint").tooltip({ 'placement': 'left' });

                        $(".js-delete-dataset").click(function(e) {
                            if (!confirm('Are you sure you want to delete this item?'))
                                return false;
                            e.preventDefault();
                            var did = $(this).attr('did');

                            $.ajax({
                                type: 'POST',
                                url: '/datasetSubmission/datasetAjaxDelete',
                                data: { 'dataset_id': did },
                                success: function(response) {
                                    if (response.success) {
                                        $('#js-dataset-row-' + did).remove();
                                    } else {
                                        alert(response.message);
                                    }
                                },
                                error: function() {}
                            });
                        });
var url = document.location.toString();
if (url.match('#')) {
    $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
}

// Change hash for page-reload
$('.nav-tabs a').on('shown.bs.tab', function (e) {
    window.location.hash = e.target.hash;
})
                    });
                    </script>