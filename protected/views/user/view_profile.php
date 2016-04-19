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
                                <a id="cancel-btn" class="btn" style="display:none"><?= Yii::t('app', 'Cancel') ?></a>
                                <?= MyHtml::submitButton(Yii::t('app', 'Save'), array('id' => 'save-btn', 'class' => 'btn-green', 'style' => 'display:none')) ?>
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
