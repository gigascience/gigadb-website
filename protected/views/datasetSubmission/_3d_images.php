<div class="row" style="margin-bottom: 5px; margin-top: 10px;">
    <div class="span9">
        <span>SketchFab 3d-Image viewer links…………………………………………………………………</span>

        <a href="#"
           id="3d_images-no"
           data-target="others-grid"
           data-target2="3d_images"
           data-url="/adminExternalLink/deleteExLinks"
           data-id="<?= $model->id ?>"
           data-type="<?= AIHelper::_3D_IMAGES ?>"
           class="btn others-button <?php if ($is3dImages === false): ?>btn-green btn-disabled<?php else: ?>js-no-button<?php endif; ?>"/>No</a>

        <a href="#"
           id="3d_images-yes"
           data-target="3d_images"
           class="btn others-button <?php if ($is3dImages === true): ?>btn-green btn-disabled<?php else: ?>js-yes-button<?php endif; ?>"/>Yes</a>
    </div>
</div>

<div class="row" id="3d_images"<?php if ($is3dImages !== true): ?> style="display: none"<?php endif; ?>>
    <div class="span9">
        <label class='control-label others-label'>Please provide SketchFab Link</label>
        <div class="controls">
            <?= CHtml::textField('link', '', array('class'=>'js-ex-link others-input', 'size' => 60, 'maxlength' => 100, 'placeholder' => "e.g. https://skfb.ly/69wDV")); ?>
        </div>
    </div>
    <div class="span2">
        <a href="#" dataset-id="<?=$model->id?>" data-type="<?= AIHelper::_3D_IMAGES ?>" class="btn js-not-allowed"/>Add Link</a>
    </div>
</div>
