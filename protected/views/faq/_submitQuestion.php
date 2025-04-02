<? if (!Yii::app()->user->hasFlash('submit-question')) {
    Yii::app()->captcha->generate();
?>

<div class="panel panel-default js-panel-always-visible" id="contact-panel">
    <div class="panel-heading">
        <h2 class="h4 panel-title" id="headingContact">
            <button data-toggle="collapse" data-parent="#accordion" data-target="#panelContact" aria-expanded="<?php echo $hasValidationErrors ? 'true' : 'false'; ?>" aria-controls="panelContact">
                Can't Find What You're Looking For?
            </button>
        </h2>
    </div>
    <div id="panelContact" class="panel-collapse collapse <?php echo $hasValidationErrors ? 'in' : ''; ?>" role="region" aria-labelledby="headingContact">
        <div class="panel-body">
            <p>Have you tried our <a href="/site/help">help pages</a>? If you still can't find the answers you are looking for, submit a question to our team here.</p>
            <?php $form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => 'form contact-form', 'id' => 'faqContactForm'))); ?>
                <?php
                $this->widget('application.components.controls.TextField', array(
                    'form' => $form,
                    'model' => $model,
                    'attributeName' => 'name',
                    'inputOptions' => array('required' => true),
                ));

                $this->widget('application.components.controls.TextField', array(
                    'form' => $form,
                    'model' => $model,
                    'attributeName' => 'email',
                    'inputOptions' => array('required' => true, 'type' => 'email'),
                ));

                $this->widget('application.components.controls.TextField', array(
                    'form' => $form,
                    'model' => $model,
                    'attributeName' => 'subject',
                    'inputOptions' => array('required' => true),
                ));

                $this->widget('application.components.controls.TextArea', array(
                    'form' => $form,
                    'model' => $model,
                    'attributeName' => 'body',
                    'inputOptions' => array('required' => true),
                ));
                ?>
                <div class="form-group">
                    <img src="<?php echo Yii::app()->captcha->output(); ?>" alt="Type the word in the image">
                </div>
                <?php
                $this->widget('application.components.controls.TextField', array(
                    'form' => $form,
                    'model' => $model,
                    'attributeName' => 'verifyCode',
                    'description' => 'Please enter the letters as they are shown in the image above.',
                    'inputOptions' => array('required' => true),
                ));
                ?>

                <div class="btns-row btns-row-end">
                    <?php echo CHtml::submitButton('Submit your question', array('class' => 'btn background-btn')); ?>
                </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>
<? } ?>