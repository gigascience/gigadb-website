<?php

use Gregwar\Captcha\CaptchaBuilder;
/**
 * Component service for handling captcha generation
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CaptchaService extends CApplicationComponent
{
    /** @var Gregwar\Captcha\CaptchaBuilder */
    protected $captchaBuilder;

    /**
     * Initialize this component
     */
    public function init()
    {
        $this->captchaBuilder = new CaptchaBuilder;
        parent::init();
    }

    /**
     * Generate text and save it to current session
     */
    public function generate(): void
    {
        $this->captchaBuilder->build();
        $_SESSION["captcha"] = $this->captchaBuilder->getPhrase();
    }

    /**
     * Generate raw image for the generated text
     *
     * @return string
     */
    public function output(): string
    {
        return $this->captchaBuilder->inline();
    }
}


?>
