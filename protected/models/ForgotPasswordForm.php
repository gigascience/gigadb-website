<?php

/**
 * ForgotPasswordForm is the data structure for keeping forgot password form 
 * data. Used by 'Forgot' action of 'ResetPasswordRequestController'.
 */
class ForgotPasswordForm extends CFormModel
{
    public $email;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            array('email', 'required'),
            array('email', 'email'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'email' => Yii::t('app', 'Email Address'),
        );
    }
}
