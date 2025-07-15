<?php

declare(strict_types=1);

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class ContactForm extends CFormModel
{
    public string $name;
    public string $email;
    public string $subject;
    public string $body;
    public string $verifyCode;

    /** For the captcha */
    public string $validacion;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('name, email, subject, body', 'required'),
            array('email', 'email'),
            array('verifyCode', 'validateCaptcha'),
        );
    }
    /**
    * Validate captcha
    */
    public function validateCaptcha(string $attribute, array $params): void
    {
        Yii::app()->captcha->validate($this, $attribute);
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'validacion' => Yii::t('CAPTCHA', 'Please type the text shown in the image: '),
        );
    }
}
