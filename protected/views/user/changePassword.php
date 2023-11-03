
<div class="content">
    <div class="container">
    <?php
        $this->widget('TitleBreadcrumb', [
          'pageTitle' => Yii::t('app', 'Change Password'),
          'breadcrumbItems' => [
            ['label' => 'Home', 'href' => '/'],
            ['isActive' => true, 'label' => 'Change Password'],
          ]
        ]);
        ?>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-xs-8 col-xs-offset-2">
            <div class="form well user-profile-box">
                <div class="panel-body">

                    <?php
                    $form = $this->beginWidget('CActiveForm', array(
                        'id' => 'ChangePassword-form',
                        'enableAjaxValidation' => false,
                        'htmlOptions' => array('class' => 'form-horizontal'),
                    ));

                        echo isset($error) && $error ? '<div class="row">' . $error . '</div>' : '';

                        $this->widget('application.components.controls.PasswordField', [
                          'form' => $form,
                          'model' => $model,
                          'attributeName' => 'password',
                          'inputOptions' => [
                            'required' => true,
                          ],
                          'labelOptions' => ['class' => 'col-xs-5'],
                          'inputWrapperOptions' => 'col-xs-5'
                        ]);
                        $this->widget('application.components.controls.PasswordField', [
                          'form' => $form,
                          'model' => $model,
                          'attributeName' => 'confirmPassword',
                          'inputOptions' => [
                            'required' => true,
                          ],
                          'labelOptions' => ['class' => 'col-xs-5'],
                          'inputWrapperOptions' => 'col-xs-5'
                        ]);

                        ?>

                        <div class="form-group checkbox-horizontal">
                          <label class="col-xs-5 control-label" for="newsletter"><?= Yii::t('app', 'Mailing list') ?></label>
                          <div class="col-xs-5">
                            <?php echo $form->checkbox($model, 'newsletter', array('id' => 'newsletter')); ?>
                            <p>Please tick here to join the GigaDB mailing list to receive news, updates and quarterly newsletters about GigaDB</p>
                          </div>
                        </div>

                        <div class="form-group checkbox-horizontal <?= $model->hasErrors('terms') ? 'has-error' : '' ?>">
                          <?= $form->label($model, 'terms', array('class' => 'col-xs-5 control-label')) ?>
                          <div class="col-xs-7">
                            <?php echo $form->checkbox($model, 'terms', array('required' => true, 'aria-required' => 'true', 'aria-describedby' => 'termsHint' . ($model->hasErrors('terms') ? ' termsError' : ''))); ?>
                            <div id="termsError" role="alert">
                              <?php echo $form->error($model, 'terms', array('class' => 'control-error help-block')); ?>
                            </div>
                            <p class="help-block" id="termsHint">Please tick here to confirm you have read and understood our <a href="/site/term#policies">Terms of use</a> and <a href="/site/term#privacy">Privacy Policy</a></p>
                          </div>
                        </div>


                    <div class="btns-row pull-right">
                      <a href="/user/view_profile" class="btn background-btn-o"><?= Yii::t('app', 'Cancel') ?></a>
                      <?php echo CHtml::submitButton(Yii::t('app', 'Save'), array('class' => 'btn background-btn m-0')); ?>
                    </div>

                  <?php $this->endWidget(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
