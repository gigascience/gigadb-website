<div class="content">
    <div class="container">
        <?php
        $this->widget('TitleBreadcrumb', [
            'pageTitle'       => Yii::t('app', 'Change Password'),
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
        <div class="col-xs-12 col-md-8 col-md-offset-2">
            <div class="form well user-profile-box">
                <div class="panel-body p-xs-0 p-sm-0">
                    <?php
                    $form = $this->beginWidget('CActiveForm', array(
                        'id'                   => 'ChangePassword-form',
                        'enableAjaxValidation' => false,
                        'htmlOptions'          => array('class' => 'form-horizontal'),
                    ));

                    echo isset($error) && $error ? '<div class="row">' . $error . '</div>' : '';

                    $this->widget('application.components.controls.PasswordField', [
                        'form'                => $form,
                        'model'               => $model,
                        'attributeName'       => 'password',
                        'inputOptions'        => [
                            'required' => true,
                        ],
                        'labelOptions'        => ['class' => 'col-xs-12 col-md-4'],
                        'inputWrapperOptions' => 'col-xs-12 col-md-8'
                    ]);
                    $this->widget('application.components.controls.PasswordField', [
                        'form'                => $form,
                        'model'               => $model,
                        'attributeName'       => 'confirmPassword',
                        'inputOptions'        => [
                            'required' => true,
                        ],
                        'labelOptions'        => ['class' => 'col-xs-12 col-md-4'],
                        'inputWrapperOptions' => 'col-xs-12 col-md-8'
                    ]);

                    ?>

                    <div class="btns-row pull-right">
                        <a href="/user/view_profile" class="btn background-btn-outline"><?= Yii::t('app', 'Cancel') ?></a>
                        <?php echo CHtml::submitButton(Yii::t('app', 'Save'), array('class' => 'btn background-btn m-0')); ?>
                    </div>

                    <?php $this->endWidget(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
