<?php

/**
 * Trait LengthWarning
 * --------------------
 *
 * Reusable helper for any Yii 1.x form control that needs to display a
 * "text too long" warning and optional live character counter.
 *
 * How to use:
 * 1. Import the trait (usually done once per file):
 *    `Yii::import('application.components.controls.traits.LengthWarning');`
 *
 * 2. Add `use LengthWarning;` inside your widget/class definition.
 *
 * 3. Expose a public `$lengthWarningOptions` property and pass the following array from the view:
 *
 *    'lengthWarningOptions' => [
 *        'threshold' => 100,            // (int) Required. Show warning above this length.
 *        'showCount' => true,           // (bool) Optional. Display live counter. Default: false.
 *        'message'   => 'Custom msg.', // (string) Optional. Default provided by the trait.
 *    ]
 *
 * 4. In your `run()` (or equivalent) method:
 *       $this->inputOptions = $this->applyLengthWarningAttributes($this->inputOptions);
 *       ...render the `<input>` / `<textarea>`...
 *       $this->renderLengthWarningPartial();
 *
 * 5. Make sure `init()` calls `$this->registerLengthWarningScript()` when
 *    `hasLengthWarning()` is true, so the JS bundle is loaded once.
 *
 * Where it can be used:
 * - Any subclass of the project's `BaseInput` (e.g. TextField, TextArea).
 */

trait LengthWarning {
    /**
     * Configuration array for the length-warning feature.
     *
     * Expected keys:
     *  - threshold (int): number of characters at which the warning appears (required)
     *  - showCount (bool): whether to show a live character counter (optional, default false)
     *  - message   (string): custom warning message (optional)
     *
     * @var array|null
     */
    public $lengthWarningOptions;

    /**
     * Checks if the feature is enabled for the current control.
     *
     * @return bool
     */
    protected function hasLengthWarning(): bool
    {
        return isset($this->lengthWarningOptions)
            && !empty($this->lengthWarningOptions)
            && array_key_exists('threshold', $this->lengthWarningOptions)
            && $this->lengthWarningOptions['threshold'] !== null
            && $this->lengthWarningOptions['threshold'] >= 0;
    }

    /**
     * Determines if the live character count should be displayed.
     *
     * @return bool
     */
    protected function showCount(): bool
    {
        return isset($this->lengthWarningOptions)
            && !empty($this->lengthWarningOptions)
            && array_key_exists('showCount', $this->lengthWarningOptions)
            && $this->lengthWarningOptions['showCount'] !== null
            && $this->lengthWarningOptions['showCount'] === true;
    }

    /**
     * Adds the HTML data-attributes needed by the length-warning JS.
     * Call this before rendering the input.
     *
     * @param array|null $inputOptions Current input options (may be null).
     * @return array Updated input options.
     */
    protected function applyLengthWarningAttributes($inputOptions): array
    {
        if (!is_array($inputOptions)) {
            $inputOptions = [];
        }

        if ($this->hasLengthWarning()) {
            $inputOptions = array_merge(
                $inputOptions,
                [
                    'data-length-threshold'   => $this->lengthWarningOptions['threshold'],
                    'data-length-show-count'  => $this->showCount() ? 'true' : 'false',
                ]
            );
        }

        return $inputOptions;
    }

    /**
     * Renders the shared partial that shows the warning and character counter.
     */
    protected function renderLengthWarningPartial(): void
    {
        if (!$this->hasLengthWarning()) {
            return;
        }

        $warningMessage = $this->getWarningMessage();
        $inputId        = \CHtml::activeId($this->model, $this->attributeName);

        \Yii::app()->controller->renderPartial(
            '//shared/_lengthWarning',
            [
                'showCount'      => $this->showCount(),
                'threshold'      => $this->lengthWarningOptions['threshold'],
                'warningMessage' => $warningMessage,
                'inputId'        => $inputId,
                'initialCount'   => mb_strlen($this->model->{$this->attributeName}),
            ]
        );
    }

    /**
     * Builds the default or user-provided warning message.
     *
     * @return string
     */
    protected function getWarningMessage(): string
    {
        if (!$this->hasLengthWarning()) {
            return '';
        }

        return $this->lengthWarningOptions['message']
            ?? "Warning: Input text is over {$this->lengthWarningOptions['threshold']} characters long, you should reduce it if possible.";
    }

    /**
     * Publishes and registers the JavaScript once per request.
     */
    protected function registerLengthWarningScript(): void
    {
        // Ensure a fresh copy during development
        \Yii::app()->assetManager->forceCopy = defined('YII_DEBUG') && YII_DEBUG;

        $jsDir  = \Yii::getAlias('/gigadb/app/client/js');
        $jsUrl  = \Yii::app()->assetManager->publish($jsDir);
        $jsPath = $jsUrl . '/length-warning.js';

        \Yii::app()->clientScript->registerScriptFile($jsPath, \CClientScript::POS_END, ['type' => 'module']);
    }
}