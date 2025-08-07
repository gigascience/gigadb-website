<div class="content col-md-offset-2 col-md-8">

    <?php
    foreach (Yii::app()->user->getFlashes() as $key => $message) {
        echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
    }
    ?>

    <div class="subsection">
        <?php
        if ($model->isNewRecord) { ?>
            <p><?= Yii::t('app', 'GigaScience appreciates your interest in the GigaDB project. With a GigaDB account, you can submit new datasets to the database. Also, GigaDB can automatically notify you of new content which matches your interests. Please fill out the following information and register to enjoy the benefits of GigaDB membership!') ?></p>
            <p class="mb-10">For more information or assistance, please contact us at: <a
                        href="mailto:database@gigasciencejournal.com" target="_blank">database@gigasciencejournal.com</a>.
            </p>
        <? }
        ?>
        <?php Yii::app()->captcha->generate(); ?>
        <div class="well">
            <? $form = $this->beginWidget('CActiveForm', array(
                'id'                   => 'user-form',
                'enableAjaxValidation' => false,
                'htmlOptions'          => array('class' => 'form-horizontal create-user-form')
            )) ?>

            <p class="mb-10 col-xs-12" aria-hidden="true">Fields with <span class="symbol">*</span> are required.</p>

            <?php if ($model->hasErrors()) : ?>
                <div class="alert alert-danger">
                    <?php echo $form->errorSummary($model); ?>
                </div>
            <?php endif; ?>

            <?php
            CHtml::$afterRequiredLabel = '<span aria-hidden="true"> *</span>';
            ?>

            <?php
            $this->widget('application.components.controls.TextField', [
                'form'                => $form,
                'model'               => $model,
                'labelOptions'        => [
                    'class' => 'col-xs-12 col-md-3',
                ],
                'inputWrapperOptions' => 'col-xs-12 col-md-9',
                'attributeName'       => 'email',
                'inputOptions'        => [
                    'required' => 'required',
                ],
            ]);
            $this->widget('application.components.controls.TextField', [
                'form'                => $form,
                'model'               => $model,
                'labelOptions'        => [
                    'class' => 'col-xs-12 col-md-3',
                ],
                'inputWrapperOptions' => 'col-xs-12 col-md-9',
                'attributeName'       => 'first_name',
                'inputOptions'        => [
                    'required' => 'required',
                ],
            ]);
            $this->widget('application.components.controls.TextField', [
                'form'                => $form,
                'model'               => $model,
                'labelOptions'        => [
                    'class' => 'col-xs-12 col-md-3',
                ],
                'inputWrapperOptions' => 'col-xs-12 col-md-9',
                'attributeName'       => 'last_name',
                'inputOptions'        => [
                    'required' => 'required',
                ],
            ]);
            $this->widget('application.components.controls.PasswordField', [
                'form'                => $form,
                'model'               => $model,
                'labelOptions'        => [
                    'class' => 'col-xs-12 col-md-3',
                ],
                'inputWrapperOptions' => 'col-xs-12 col-md-9',
                'attributeName'       => 'password',
                'inputOptions'        => [
                    'required' => 'update' !== $scenario ? 'required' : false,
                ],
            ]);
            $this->widget('application.components.controls.PasswordField', [
                'form'                => $form,
                'model'               => $model,
                'labelOptions'        => [
                    'class' => 'col-xs-12 col-md-3',
                ],
                'inputWrapperOptions' => 'col-xs-12 col-md-9',
                'attributeName'       => 'password_repeat',
                'inputOptions'        => [
                    'required' => 'update' !== $scenario ? 'required' : false,
                ],
            ]);


            ?>

            <?php
            $this->widget('application.components.controls.TextField', [
                'form'                => $form,
                'model'               => $model,
                'labelOptions'        => [
                    'class' => 'col-xs-12 col-md-3',
                ],
                'inputWrapperOptions' => 'col-xs-12 col-md-9',
                'attributeName'       => 'affiliation',
                'inputOptions'        => [
                    'required' => 'required',
                ],
            ]);
            ?>
            <div class="form-group">
                <?= $form->labelEx($model, 'preferred_link', array('class' => 'col-xs-3 control-label')) ?>
                <div class="col-md-9 input-wrapper">
                    <?= CHtml::activeDropDownList($model, 'preferred_link', User::$linkouts, array('class' => 'form-control', 'aria-describedby' => $model->hasErrors('preferred_link') ? 'preferred_link-error' : '')) ?>
                    <div id="preferred_link-error"><?= $form->error($model, 'preferred_link', array('class' => 'control-error help-block')) ?></div>
                </div>
            </div>
            <div class="form-group checkbox-horizontal  <?= $model->hasErrors('newsletter') ? 'has-error' : '' ?>">
                <label class="col-md-3 control-label"
                       for="User_newsletter"><?= Yii::t('app', 'Mailing list') ?></label>
                <div class="col-md-9 input-wrapper">
                    <?php echo $form->checkbox($model, 'newsletter', array('aria-describedby' => 'newsletter-desc')); ?>
                </div>
                <div class="col-md-9 help-block checkbox-desc" id="newsletter-desc">
                    <p>Please tick here to join the GigaDB mailing list to receive news, updates and quarterly
                        newsletters about GigaDB</p>
                </div>
            </div>
            <? if ('create' === $scenario) { ?>
                <div class="form-group checkbox-horizontal <?= $model->hasErrors('terms') ? 'has-error' : '' ?>">
                    <?= $form->labelEx($model, 'terms', array('class' => 'col-md-3 control-label')) ?>
                    <div class="col-md-9 input-wrapper">
                        <?php echo $form->checkbox($model, 'terms', array('aria-describedby' => $model->hasErrors('terms') ? 'terms-error terms-desc' : 'terms-desc', 'required' => true, 'aria-required' => 'true')); ?>
                    </div>
                    <div class='col-md-9 checkbox-error' id="terms-error"><?= $form->error($model, 'terms', array('class' => 'control-error help-block')) ?></div>
                    <div id="terms-desc" class="col-md-9 help-block checkbox-desc"><p>Please tick here to confirm you have read and understood
                        our <a href="/site/term#policies">Terms of use</a> and <a href="/site/term#privacy">Privacy Policy</a></p>
                    </div>
                </div>
            <? } ?>


            <? if ($model->isNewRecord) { ?>
                <div class="form-group <?= $model->hasErrors('verifyCode') ? 'has-error' : '' ?>">
                    <?php echo $form->labelEx($model, 'verifyCode', array('class' => 'col-xs-3 control-label')); ?>
                    <div class="col-md-9 input-wrapper">
                        <div class='captcha mb-10'>
                            <img class='captcha-image test-captcha-image' src="<?php echo Yii::app()->captcha->output(); ?>"
                                 alt="Type the word in the image">
                        </div>
                        <?php echo $form->textField($model, 'verifyCode', array('class' => 'form-control', 'aria-describedby' => $model->hasErrors('verifyCode') ? 'verifyCode-error verifyCode-desc' : 'verifyCode-desc')); ?>
                        <div id="verifyCode-desc" class="hint control-description help-block">Please enter the letters
                            as they are shown in the image above.
                            <br />Letters are case-sensitive.
                        </div>
                        <div id="verifyCode-error">
                            <?php echo $form->error($model, 'verifyCode', array('class' => 'control-error help-block')); ?>
                        </div>
                    </div>
                </div>
            <? } ?>
            <hr>
            <div class="pull-right btns-row btns-row-end">
                <?= CHtml::submitButton($model->isNewRecord ? Yii::t('app', 'Register') : 'Save', array('class' => 'btn background-btn submit-btn')) ?>
            </div>
            <div class="clearfix"></div>
            <? $this->endWidget() ?>
        </div><!--well-->


        <?php
        $path = "images/tempcaptcha/" . $text . ".png";
        $files = glob('images/tempcaptcha/*');
        foreach ($files as $file) {
            if (is_file($file))
                if ($file != $path)
                    unlink($file);
        }
        ?>
    </div>
    <!-- user-form -->
</div>
