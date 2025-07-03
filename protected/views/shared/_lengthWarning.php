<?php
/**
 * Length Warning Partial View
 * Renders dynamic character count and warning message
 */

$inputId = isset($inputId) ? CHtml::encode($inputId) : '';
?>
<div class="length-warning-wrapper">
<?php if ($showCount) : ?>
    <div class="length-warning-display">
        <span id="<?php echo $inputId ?>-length-count"><?php echo $initialCount ?> / <?php echo $threshold ?> characters.</span>
<?php endif; ?>

        <div id="<?php echo $inputId ?>-length-warning" role="status" aria-live="polite">
            <div class="js-length-warning-message length-warning-message" style="display:none;">
                <span class="fa fa-exclamation-triangle icon-warn"></span>
                <?php echo CHtml::encode($warningMessage) ?>
            </div>
        </div>

<?php if ($showCount) : ?>
    </div>
<?php endif; ?>
</div>