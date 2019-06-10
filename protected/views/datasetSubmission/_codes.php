<div class="row" style="margin-bottom: 5px; margin-top: 10px;">
    <div class="span9">
        <span>Actionable code in CodeOceans…………………………………………………………………..</span>

        <a href="#"
           id="codes-no"
           data-target="others-grid"
           data-target2="codes"
           data-url="/adminExternalLink/deleteExLinks"
           data-id="<?= $model->id ?>"
           data-type="<?= AIHelper::CODES ?>"
           class="btn others-button <?php if ($isCodes === false): ?>btn-green btn-disabled<?php else: ?>js-no-button<?php endif; ?>"/>No</a>

        <a href="#"
           id="codes-yes"
           data-target="codes"
           class="btn others-button <?php if ($isCodes === true): ?>btn-green btn-disabled<?php else: ?>js-yes-button<?php endif; ?>"/>Yes</a>
    </div>
</div>

<div class="row" id="codes"<?php if ($isCodes !== true): ?> style="display: none"<?php endif; ?>>
    <div class="span9">
        <label class='control-label others-label' style="margin-left: 0;width: 100%;">Please provide CodeOceans “Embed code widget”:</label>
        <div style="margin-left: 10px;">
            <?= CHtml::textField('link', '', array('class'=>'js-ex-link others-input', 'size' => 60, 'style' => 'width: 100%;', 'placeholder' => "<script src=\"https://codeocean.com/widget.js?id=0a812d9b-0ff3-4eb7-825f-76d3cd049a43\" async></script>")); ?>
        </div>
    </div>
    <div class="span2">
        <a href="#" dataset-id="<?=$model->id?>" data-type="<?= AIHelper::CODES ?>" class="btn js-not-allowed" style="margin-top: 29px;"/>Add Link</a>
    </div>
</div>
