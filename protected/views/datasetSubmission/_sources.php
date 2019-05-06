<div class="row" style="margin-bottom: 5px; margin-top: 10px;">
    <div class="span9">
        <span>or any other URL to a stable source of data and files directly related to this dataset………..</span>

        <a href="#"
           id="sources-no"
           data-target="others-grid"
           data-target2="sources"
           data-url="/adminExternalLink/deleteExLinks"
           data-id="<?= $model->id ?>"
           data-type="<?= AIHelper::SOURCES ?>"
           class="btn others-button <?php if ($isSources === false): ?>btn-green btn-disabled<?php else: ?>js-no-button<?php endif; ?>"/>No</a>

        <a href="#"
           id="sources-yes"
           data-target="sources"
           class="btn others-button <?php if ($isSources === true): ?>btn-green btn-disabled<?php else: ?>js-yes-button<?php endif; ?>"/>Yes</a>
    </div>
</div>

<div class="row" id="sources"<?php if ($isSources !== true): ?> style="display: none"<?php endif; ?>>
    <div class="span9">
        <label class='control-label others-label' style="margin-left: 0;width: 100%;font-weight: normal;">Where possible use the DOI instead of a URL.</label>
        <label class='control-label others-label' style="margin-left: 0;width: 100%;">Please provide the DOI or URL:</label>
        <div>
            <?= CHtml::textField('link', '', array('class'=>'js-ex-link', 'size' => 60, 'style' => 'width: 350px;', 'placeholder' => "e.g. doi:12.3456/789012.3")); ?>
            <?= CHtml::textArea('link', '', array('class'=>'js-ex-description', 'size' => 60, 'style' => 'width: 326px;', 'placeholder' => "Short description of what the link is to")); ?>
        </div>
    </div>
    <div class="span2">
        <br><br><br>
        <a href="#" dataset-id="<?=$model->id?>" data-type="<?= AIHelper::SOURCES ?>" class="btn js-not-allowed"/>Add Link</a>
    </div>
</div>
