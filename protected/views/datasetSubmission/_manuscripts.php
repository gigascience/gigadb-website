<div class="row" style="margin-bottom: 5px;">
    <div class="span9">
        <span>A published manuscript that uses this data………………………………………………………</span>

        <a href="#"
           id="manuscripts-no"
           data-target="others-grid"
           data-target2="manuscripts"
           data-url="/adminExternalLink/deleteExLinks"
           data-id="<?= $model->id ?>"
           data-type="<?= AIHelper::MANUSCRIPTS ?>"
           class="btn others-button <?php if ($isManuscripts === false): ?>btn-green btn-disabled<?php else: ?>js-no-button<?php endif; ?>"/>No</a>

        <a href="#"
           id="manuscripts-yes"
           data-target="manuscripts"
           class="btn others-button <?php if ($isManuscripts === true): ?>btn-green btn-disabled<?php else: ?>js-yes-button<?php endif; ?>"/>Yes</a>
    </div>
</div>

<div class="row" id="manuscripts"<?php if ($isManuscripts !== true): ?> style="display: none"<?php endif; ?>>
    <div class="span9">
        <div class="controls">
            <?= CHtml::textField('link', '', array('class'=>'js-ex-link others-input', 'size' => 60, 'maxlength' => 100, 'placeholder' => "e.g. doi:10.1093/gigascience/giy095")); ?>
        </div>
    </div>
    <div class="span2">
        <a href="#" dataset-id="<?=$model->id?>" data-type="<?= AIHelper::MANUSCRIPTS ?>" class="btn js-not-allowed"/>Add Link</a>
    </div>
</div>
