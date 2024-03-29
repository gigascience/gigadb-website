<?php

class ResetPasswordForm extends CFormModel
{
    public $password;
    public $confirmPassword;
    public $user_id;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            array('password, confirmPassword, user_id', 'required'),
            array('password', 'compare', 'compareAttribute'=>'confirmPassword'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'password'=>Yii::t('app' ,'Password'),
            'confirmPassword'=>Yii::t('app' ,'Confirm Password'),
        );
    }

    public function changePass(){
        $user = User::model()->findByPk($this->user_id);
        if(isset($user)){
            $user->password = $this->password;
            $user->password_repeat = $this->confirmPassword;
            $user->encryptPassword();

            if($user->save(false)) {
                return true;
            }
        }
        return false;
    }
}
