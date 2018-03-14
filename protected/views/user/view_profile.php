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
    <font color="red">
    <div>
        <?= Yii::app()->user->getFlash('error'); ?>
    </div>
    </font>
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
                            <li role="presentation"><a href="#new" aria-controls="new" role="tab" data-toggle="tab">Submit new dataset</a></li>
                            <li role="presentation"><a href="#submitted" aria-controls="submitted" role="tab" data-toggle="tab">Your datasets</a></li>
                            <li role="presentation"><a href="#saved" aria-controls="saved" role="tab" data-toggle="tab">Saved search</a></li>

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
                                <label class="col-xs-5 control-label"><?= $model->email ?></label>
                                <br>
                                <br>
                                <?= $form->textField($model, 'email', array('size' => 30, 'maxlength' => 128, 'class' => 'profile-textbox', 'style' => 'display:none')) ?>
                                <?= $form->error($model, 'email') ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= $form->label($model, 'first_name', array('class' => 'col-xs-5 control-label')) ?>
                            <div class="col-xs-5">
                                <label class="col-xs-5 control-label"><?= $model->first_name ?></label>
                                <br>
                                <?= $form->textField($model, 'first_name', array('size' => 30, 'maxlength' => 60, 'class' => 'profile-textbox', 'style' => 'display:none')) ?>
                                <?= $form->error($model, 'first_name') ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= $form->label($model, 'last_name', array('class' => 'col-xs-5 control-label')) ?>
                            <div class="col-xs-5">
                                <label class="col-xs-5 control-label"><?= $model->last_name ?></label>
                                <br>
                                <?= $form->textField($model, 'last_name', array('size' => 30, 'maxlength' => 60, 'class' => 'profile-textbox', 'style' => 'display:none')) ?>
                                <?= $form->error($model, 'last_name') ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= $form->label($model, 'affiliation', array('class' => 'col-xs-5 control-label')) ?>
                            <div class="col-xs-5">
                                <label class="col-xs-5 control-label"><?= $model->affiliation ?></label>
                                <br>
                                <?= $form->textField($model, 'affiliation', array('size' => 30, 'maxlength' => 60, 'class' => 'profile-textbox', 'style' => 'display:none')) ?>                               
                                <?= $form->error($model, 'affiliation') ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= $form->labelEx($model,'preferred_link', array('class'=>'col-xs-5 control-label')) ?>
                            <div class="col-xs-5">
                                <label class="col-xs-5 control-label"><?= $model->preferred_link ?></label>
                                <?= CHtml::activeDropDownList($model,'preferred_link', User::$linkouts, array('class'=>'profile-textbox','style'=>'display:none')) ?>
                                <?= $form->error($model,'preferred_link') ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="text-center">
                                <?php echo $form->checkbox($model, 'newsletter', array('disabled' => 'disabled;', 'class' => 'checkbox','style' =>'position: relative; display: inline-block')); ?>
                                <label disabled="disabled" ><?= Yii::t('app', 'Add me to GigaDB\'s mailing list') ?></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="text-center">
                            <div class="controls">
                                <a id="cancel-btn" class="btn background-btn" style="display:none"><?= Yii::t('app', 'Cancel') ?></a>
                                <?= MyHtml::submitButton(Yii::t('app', 'Save'), array('id' => 'save-btn', 'class' => 'btn background-btn', 'style' => 'display:none')) ?>
                            </div>
                            <br>
                            <button id="edit-btn" type="button" class="btn background-btn">Edit</button>
                            <a href="/user/changePassword" class="btn background-btn"><?= Yii::t('app', 'Change Password') ?></a>
                        </div>
                        </div>
                    </div><!--well-->

                    <? $this->endWidget() ?>
                                    </div>
                                
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane" id="new">
                            <div class="color-background color-background-block" style="margin-bottom: 20px;">
                                <p>GigaDB primarily serves as a repository to host data and tools associated with articles in GigaScience; however, it also includes a subset of datasets that are not associated with GigaScience articles (see below). GigaDB defines a dataset as a group of files (e.g., sequencing data, analyses, imaging files, software programs) that are related to and support an article or study. Through our association with DataCite, each dataset in GigaDB will be assigned a DOI that can be used as a standard citation for future use of these data in other articles by the authors and other researchers.</p>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="underline-title">
                                        <div>
                                            <h4>Dataset upload</h4>
                                        </div>
                                    </div>
                                    <p style="margin-bottom: 20px;">You will need to fill out a template file and then give it a new file name.</p>
                                    <p>Click 'Download Template File' to get a copy:</p>
                                    <p style="margin-bottom: 20px;"><button class="btn background-btn background-btn-o"><i class="fa fa-download"></i> Download Template File</button></p>
                                    <p>When filling out your dataset file, you may refer to the files below as examples.</p>
                                    <p><a class="btn background-btn background-btn-o" href="#"><i class="fa fa-download"></i> Download Example File 1</a></p>
                                    <p style="margin-bottom: 25px;"><a class="btn background-btn background-btn-o" href="#"><i class="fa fa-download"></i> Download Example File 2</a></p>
                                    <div class="checkbox" style="margin-bottom: 20px;">
                                        <input type="checkbox" id="read0" name="read0">
                                        <label for="read0">
                                            <a href="#">I have read GigaDB's Terms and Conditions</a>
                                        </label>
                                    </div>
                                    <table class="table new-upload-table" style="width: 85%;">
                                        <tbody>
                                            <tr>
                                                <td colspan="2">
                                                    <label>Excel file</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 99%;">
                                                    <div class="input-group">
                                                        <input class="form-control" autocomplete="off" type="text">
                                                        <span class="input-group-btn">
                                                            <button class="btn background-btn" type="file">Files</button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="btn background-btn" type="button" style="margin-left: 20px;"><i class="fa fa-upload"></i> Upload new dataset</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-xs-6">
                                    <div class="underline-title">
                                        <div>
                                            <h4>Online submission</h4>
                                        </div>
                                    </div>
                                    <p style="margin-bottom: 20px;">Provide all the infomation required for submission via a series of web-forms:</p>
                                    <ol style="margin-bottom: 20px;">
                                        <li>Study details</li>
                                        <li>Authors</li>
                                        <li>Project details</li>
                                        <li>Links and related datasets</li>
                                        <li>Sample information</li>
                                    </ol>
                                    <div class="checkbox" style="margin-bottom: 20px;">
                                        <input type="checkbox" id="read1" name="read1">
                                        <label for="read1">
                                            <a href="#">I have read GigaDB's Terms and Conditions</a>
                                        </label>
                                    </div>
                                    <button class="btn background-btn" type="button"><i class="fa fa-share"></i> Submission wizard</button>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="submitted">
                            <section>
                                <table class="table table-bordered submitted-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%;">DOI</th>
                                            <th>Title</th>
                                            <th>Subject</th>
                                            <th>Dataset type</th>
                                            <th>Status</th>
                                            <th style="width: 1%;">Publication date</th>
                                            <th style="width: 1%;">Modification date</th>
                                            <th style="width: 1%;">File count</th>
                                            <th style="width: 1%;">Operation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>10.6736/137643</td>
                                            <td>Some title some title some title Some title some title some title</td>
                                            <td>Subject subject subject</td>
                                            <td>Data set type</td>
                                            <td>The status</td>
                                            <td>2018-01-01</td>
                                            <td>2018-01-01</td>
                                            <td>20</td>
                                            <td><a href="#"><i class="fa fa-edit"></i></a><a href="#"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>10.6736/137643</td>
                                            <td>Some title some title some title Some title some title some title</td>
                                            <td>Subject subject subject</td>
                                            <td>Data set type</td>
                                            <td>The status</td>
                                            <td>2018-01-01</td>
                                            <td>2018-01-01</td>
                                            <td>20</td>
                                            <td><a href="#"><i class="fa fa-edit"></i></a><a href="#"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>10.6736/137643</td>
                                            <td>Some title some title some title Some title some title some title</td>
                                            <td>Subject subject subject</td>
                                            <td>Data set type</td>
                                            <td>The status</td>
                                            <td>2018-01-01</td>
                                            <td>2018-01-01</td>
                                            <td>20</td>
                                            <td><a href="#"><i class="fa fa-edit"></i></a><a href="#"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>10.6736/137643</td>
                                            <td>Some title some title some title Some title some title some title</td>
                                            <td>Subject subject subject</td>
                                            <td>Data set type</td>
                                            <td>The status</td>
                                            <td>2018-01-01</td>
                                            <td>2018-01-01</td>
                                            <td>20</td>
                                            <td><a href="#"><i class="fa fa-edit"></i></a><a href="#"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>10.6736/137643</td>
                                            <td>Some title some title some title Some title some title some title</td>
                                            <td>Subject subject subject</td>
                                            <td>Data set type</td>
                                            <td>The status</td>
                                            <td>2018-01-01</td>
                                            <td>2018-01-01</td>
                                            <td>20</td>
                                            <td><a href="#"><i class="fa fa-edit"></i></a><a href="#"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>10.6736/137643</td>
                                            <td>Some title some title some title Some title some title some title</td>
                                            <td>Subject subject subject</td>
                                            <td>Data set type</td>
                                            <td>The status</td>
                                            <td>2018-01-01</td>
                                            <td>2018-01-01</td>
                                            <td>20</td>
                                            <td><a href="#"><i class="fa fa-edit"></i></a><a href="#"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>10.6736/137643</td>
                                            <td>Some title some title some title Some title some title some title</td>
                                            <td>Subject subject subject</td>
                                            <td>Data set type</td>
                                            <td>The status</td>
                                            <td>2018-01-01</td>
                                            <td>2018-01-01</td>
                                            <td>20</td>
                                            <td><a href="#"><i class="fa fa-edit"></i></a><a href="#"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>10.6736/137643</td>
                                            <td>Some title some title some title Some title some title some title</td>
                                            <td>Subject subject subject</td>
                                            <td>Data set type</td>
                                            <td>The status</td>
                                            <td>2018-01-01</td>
                                            <td>2018-01-01</td>
                                            <td>20</td>
                                            <td><a href="#"><i class="fa fa-edit"></i></a><a href="#"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>10.6736/137643</td>
                                            <td>Some title some title some title Some title some title some title</td>
                                            <td>Subject subject subject</td>
                                            <td>Data set type</td>
                                            <td>The status</td>
                                            <td>2018-01-01</td>
                                            <td>2018-01-01</td>
                                            <td>20</td>
                                            <td><a href="#"><i class="fa fa-edit"></i></a><a href="#"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>10.6736/137643</td>
                                            <td>Some title some title some title Some title some title some title</td>
                                            <td>Subject subject subject</td>
                                            <td>Data set type</td>
                                            <td>The status</td>
                                            <td>2018-01-01</td>
                                            <td>2018-01-01</td>
                                            <td>20</td>
                                            <td><a href="#"><i class="fa fa-edit"></i></a><a href="#"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </section>
                            <div class="text-center pagination-component">
                                <ul class="pagination">
                                    <li class="disabled"><a href="#"><span>First</span></a></li>
                                    <li class="disabled"><a href="#"><span>Previous</span></a></li>
                                    <li class="active"><a href="#">1</a></li>
                                    <li><a href="#">2</a></li>
                                    <li><a href="#">3</a></li>
                                    <li><a href="#">4</a></li>
                                    <li><a href="#">5</a></li>
                                    <li><a href="#"><span>Next</span></a></li>
                                    <li><a href="#"><span>Last</span></a></li>
                                </ul>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="saved">
                            <section>
                                <table class="table table-bordered saved-table text-center">
                                    <thead>
                                        <tr>
                                            <th>Keyword</th>
                                            <th>Result</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Subject subject subject</td>
                                            <td>Some title some title some title Some title some title some title</td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Subject subject subject</td>
                                            <td>Some title some title some title Some title some title some title</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </section>
                            <div class="text-center pagination-component">
                                <ul class="pagination">
                                    <li class="disabled"><a href="#"><span>First</span></a></li>
                                    <li class="disabled"><a href="#"><span>Previous</span></a></li>
                                    <li class="active"><a href="#">1</a></li>
                                    <li><a href="#">2</a></li>
                                    <li><a href="#">3</a></li>
                                    <li><a href="#">4</a></li>
                                    <li><a href="#">5</a></li>
                                    <li><a href="#"><span>Next</span></a></li>
                                    <li><a href="#"><span>Last</span></a></li>
                                </ul>
                            </div>
                            
                        </div>
                    </div>
                </section>
            </div>
        </div>





<h2><?= Yii::t('app', 'Your Profile Page') ?></h2>
<div class="clear"></div>
<table align="center">
    <tr >
        <td style="left:50px">
            <div class="row">
                <div>
                    <div class="form well user-profile-box">
                        <?php
                        $form = $this->beginWidget('CActiveForm', array(
                            'id' => 'EditProfile-form',
                            'enableAjaxValidation' => false,
                            'htmlOptions' => array('class' => 'form-horizontal'),
                        ));
                        ?>
                        <div class="control-group">
                            <?= $form->label($model, 'email', array('class' => 'control-label')) ?>
                            <div class="controls">
                                <label class="profile-label"><?= $model->email ?></label>
                                <?= $form->textField($model, 'email', array('size' => 30, 'maxlength' => 128, 'class' => 'profile-textbox', 'style' => 'display:none')) ?>
                                <?= $form->error($model, 'email') ?>
                            </div>
                        </div>

                        <div class="control-group">
                            <?= $form->label($model, 'first_name', array('class' => 'control-label')) ?>
                            <div class="controls">
                                <label class="profile-label"><?= $model->first_name ?></label>
                                <?= $form->textField($model, 'first_name', array('size' => 30, 'maxlength' => 60, 'class' => 'profile-textbox', 'style' => 'display:none')) ?>
                                <?= $form->error($model, 'first_name') ?>
                            </div>
                        </div>

                        <div class="control-group">
                            <?= $form->label($model, 'last_name', array('class' => 'control-label')) ?>
                            <div class="controls">
                                <label class="profile-label"><?= $model->last_name ?></label>
                                <?= $form->textField($model, 'last_name', array('size' => 30, 'maxlength' => 60, 'class' => 'profile-textbox', 'style' => 'display:none')) ?>
                                <?= $form->error($model, 'last_name') ?>
                            </div>
                        </div>

                        <div class="control-group">
                            <?= $form->label($model, 'affiliation', array('class' => 'control-label')) ?>
                            <div class="controls">
                                <label class="profile-label"><?= $model->affiliation ?></label>
                                <?= $form->textField($model, 'affiliation', array('size' => 30, 'maxlength' => 60, 'class' => 'profile-textbox', 'style' => 'display:none')) ?>
                                <?= $form->error($model, 'affiliation') ?>
                            </div>
                        </div>

                        <div class="control-group">
                            <?= $form->labelEx($model,'preferred_link', array('class'=>'control-label')) ?>
                            <div class="controls">
                                <label class="profile-label"><?= $model->preferred_link ?></label>
                                <?= CHtml::activeDropDownList($model,'preferred_link', User::$linkouts, array('class'=>'profile-textbox','style'=>'display:none')) ?>
                                <?= $form->error($model,'preferred_link') ?>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <?php echo $form->checkbox($model, 'newsletter', array('disabled' => 'disabled;', 'class' => 'profile-checkbox')); ?>
                                <label disabled="disabled"><?= Yii::t('app', 'Add me to GigaDB\'s mailing list') ?></label>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <a id="cancel-btn" class="btn background-btn" style="display:none"><?= Yii::t('app', 'Cancel') ?></a>
                                <?= MyHtml::submitButton(Yii::t('app', 'Save'), array('id' => 'save-btn', 'class' => 'btn background-btn', 'style' => 'display:none')) ?>
                            </div>
                        </div>
                    </div><!--well-->

                    <? $this->endWidget() ?>
                </div><!--span8-->
            </div><!-- user-form -->
        </td>
        <td style="padding-left: 50px;">
            <div class="row">
                <div class="form">
                    <div class="left4">


                        <div class="control-group">
                            <div class="controles">
                                <a id="edit-btn" class="btn-green"><?= Yii::t('app', 'Edit Personal Details') ?></a>

                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <a href="/user/changePassword" class="btn-green"><?= Yii::t('app', 'Change Password') ?></a>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <a class="btn-green" href="/dataset/upload" >
                                    Submit new dataset</a>

                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </td>
    </tr>
</table>

    <div class="clear"></div>

    <?= $this->renderPartial('uploadedDatasets', array('uploadedDatasets' => $uploadedDatasets)); ?>
    <?= $this->renderPartial('searches', array('searchRecord' => $searchRecord)); ?>






    <script>

        $('#edit-btn').click(function(e) {
            e.preventDefault();
            $('#save-btn').css('display', '');
            $('#cancel-btn').css('display', '');
            $('#edit-btn').css('display', 'none');
            $('.profile-label').css('display', 'none');
            $('.profile-textbox').css('display', '');
            $('.profile-checkbox').attr('disabled', false);
        });
        $('#cancel-btn').click(function(e) {
            e.preventDefault();
            $('#save-btn').css('display', 'none');
            $('#cancel-btn').css('display', 'none');
            $('#edit-btn').css('display', '');
            $('.profile-label').css('display', '');
            $('.profile-textbox').css('display', 'none');
            $('.profile-checkbox').attr('disabled', true);
        });

        var el = document.getElementById('test2');
        el.onclick = close();


        function redirect(url) {
            document.getElementById('dialogDisplay').dialog("close");
        }

        function close() {
            $('#displayDialog').dialog('close');
        }
    </script>
