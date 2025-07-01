<?php
$inputId = isset($inputId) ? CHtml::encode($inputId) : '';
?>
<div class="length-warning-wrapper">
<?php if ($showCount): ?>
    <div class="length-warning-display">
        <span id="<?= $inputId ?>-length-count"><?= $initialCount ?> / <?= $threshold ?> characters.</span>
<?php endif; ?>

        <div id="<?= $inputId ?>-length-warning" role="status" aria-live="polite">
            <div class="js-length-warning-message length-warning-message" style="display:none;">
                <span class="fa fa-exclamation-triangle icon-warn"></span>
                <?= CHtml::encode($warningMessage) ?>
            </div>
        </div>

<?php if ($showCount): ?>
    </div>
<?php endif; ?>
</div>