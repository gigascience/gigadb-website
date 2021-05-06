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
                            <section class="page-title-section">
                                <div class="page-title">
                                    <ol class="breadcrumb pull-right">
                                        <li><a href="/">Home</a></li>
                                        <li class="active">Your profile</li>
                                    </ol>
                                    <h4>Your profile page</h4>
                                </div>
                            </section>
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
                                                    <?php
                        $form = $this->beginWidget('CActiveForm', array(
                            'id' => 'EditProfile-form',
                            'enableAjaxValidation' => false,
                            'htmlOptions' => array('class' => 'form-horizontal'),
                        ));
                        ?>
                                                        <div class="form-group">
                                                            <?= $form->label($model, 'email', array('class' => 'col-xs-5 control-label')) ?>
                                                                <div class="col-xs-5">
                                                                    <label class="profile-label" style="padding-right: 0px;">
                                                                        <?= $model->email ?>
                                                                    </label>
                                                                    <?= $form->textField($model, 'email', array('size' => 30, 'maxlength' => 128, 'class' => 'profile-textbox', 'style' => 'display:none')) ?>
                                                                        <?= $form->error($model, 'email') ?>
                                                                </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <?= $form->label($model, 'first_name', array('class' => 'col-xs-5 control-label')) ?>
                                                                <div class="col-xs-5">
                                                                    <label class="profile-label" style="padding-right: 0px;">
                                                                        <?= $model->first_name ?>
                                                                    </label>
                                                                    <?= $form->textField($model, 'first_name', array('size' => 30, 'maxlength' => 60, 'class' => 'profile-textbox', 'style' => 'display:none')) ?>
                                                                        <?= $form->error($model, 'first_name') ?>
                                                                </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <?= $form->label($model, 'last_name', array('class' => 'col-xs-5 control-label')) ?>
                                                                <div class="col-xs-5">
                                                                    <label class="profile-label" style="padding-right: 0px;">
                                                                        <?= $model->last_name ?>
                                                                    </label>
                                                                    <?= $form->textField($model, 'last_name', array('size' => 30, 'maxlength' => 60, 'class' => 'profile-textbox', 'style' => 'display:none')) ?>
                                                                        <?= $form->error($model, 'last_name') ?>
                                                                </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <?= $form->label($model, 'affiliation', array('class' => 'col-xs-5 control-label')) ?>
                                                                <div class="col-xs-5">
                                                                    <label class="profile-label" style="padding-right: 0px;">
                                                                        <?= $model->affiliation ?>
                                                                    </label>
                                                                    <?= $form->textField($model, 'affiliation', array('size' => 30, 'maxlength' => 60, 'class' => 'profile-textbox', 'style' => 'display:none')) ?>
                                                                        <?= $form->error($model, 'affiliation') ?>
                                                                </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <?= $form->labelEx($model,'preferred_link', array('class'=>'col-xs-5 control-label')) ?>
                                                                <div class="col-xs-5">
                                                                    <label class="profile-label" style="padding-right: 0px;">
                                                                        <?= $model->preferred_link ?>
                                                                    </label>
                                                                    <?= CHtml::activeDropDownList($model,'preferred_link', User::$linkouts, array('class'=>'profile-textbox','style'=>'display:none')) ?>
                                                                        <?= $form->error($model,'preferred_link') ?>
                                                                </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="text-center">
                                                                <?php echo $form->checkbox($model, 'newsletter', array('disabled' => 'disabled;', 'class' => 'checkbox','style' =>'position: relative; display: inline-block')); ?>
                                                                <label disabled="disabled">
                                                                    <?= Yii::t('app', 'Add me to GigaDB\'s mailing list') ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="text-center">
                                                                <div class="controls">
                                                                    <a id="cancel-btn" class="btn background-btn" style="display:none">
                                                                        <?= Yii::t('app', 'Cancel') ?>
                                                                    </a>
                                                                    <?= CHtml::submitButton(Yii::t('app', 'Save'), array('id' => 'save-btn', 'class' => 'btn background-btn', 'style' => 'display:none')) ?>
                                                                </div>
                                                                <br>
                                                                <button id="edit-btn" type="button" class="btn background-btn">Edit</button>
                                                                <a href="/user/changePassword" class="btn background-btn">
                                                                    <?= Yii::t('app', 'Change Password') ?>
                                                                </a>
                                                                <a href="/datasetSubmission/upload" class="btn background-btn">
                                                                    <?= Yii::t('app', 'Submit new dataset') ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                </div>
                                                <!--well-->
                                                <? $this->endWidget() ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="submitted">
                                        <?= $this->renderPartial('uploadedDatasets', array('uploadedDatasets' => $uploadedDatasets)); ?>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="authored">
                                        <?= $this->renderPartial('authoredDatasets', array('authoredDatasets' => $authoredDatasets,'linkedAuthors' => $linkedAuthors)); ?>
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

                        $('#edit-btn').on('click', function (e) {
                            e.preventDefault();
                            $('#save-btn').css('display', '');
                            $('#cancel-btn').css('display', '');
                            $('#edit-btn').css('display', 'none');
                            $('.profile-label').css('display', 'none');
                            $('.profile-textbox').css('display', '');
                            $('.profile-checkbox').attr('disabled', false);
                        });
                        $('#cancel-btn').on('click', function (e) {
                            e.preventDefault();
                            $('#save-btn').css('display', 'none');
                            $('#cancel-btn').css('display', 'none');
                            $('#edit-btn').css('display', '');
                            $('.profile-label').css('display', '');
                            $('.profile-textbox').css('display', 'none');
                            $('.profile-checkbox').attr('disabled', true);
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