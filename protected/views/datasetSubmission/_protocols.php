<div class="row" style="margin-bottom: 5px; margin-top: 10px;">
    <div class="span9">
        <span>Protocols.io link to methods used to generate this data………………………………………..</span>

        <a href="#"
           id="protocols-no"
           data-target="others-grid"
           data-target2="protocols"
           data-url="/adminExternalLink/deleteExLinks"
           data-id="<?= $model->id ?>"
           data-type="<?= AIHelper::PROTOCOLS ?>"
           class="btn others-button <?php if ($isProtocols === false): ?>btn-green btn-disabled<?php else: ?>js-no-button<?php endif; ?>"/>No</a>

        <a href="#"
           id="protocols-yes"
           data-target="protocols"
           class="btn others-button <?php if ($isProtocols === true): ?>btn-green btn-disabled<?php else: ?>js-yes-button<?php endif; ?>"/>Yes</a>
    </div>
</div>

<div class="row" id="protocols"<?php if ($isProtocols !== true): ?> style="display: none"<?php endif; ?>>
    <div class="span9">
        <label class='control-label others-label'>Please provide the Protocols.io DOI</label>
        <div class="controls">
            <?= CHtml::textField('link', '', array('class'=>'js-ex-link others-input', 'size' => 60, 'maxlength' => 100, 'placeholder' => "e.g. doi:10.17504/protocols.io.gk8buzw")); ?>
        </div>
    </div>
    <div class="span2">
        <a href="#" dataset-id="<?=$model->id?>" data-type="<?= AIHelper::PROTOCOLS ?>" class="btn js-not-allowed"/>Add Link</a>
    </div>
</div>