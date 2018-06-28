<?php

class ChangePasswordForm extends CFormModel
{
	public $password;
	public $confirmPassword;
	public $user_id;
        public $terms;
        public $newsletter;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('password, confirmPassword, user_id, terms', 'required'),
                        array('password', 'compare', 'compareAttribute'=>'confirmPassword'),
                        array('terms','compare', 'compareValue' => TRUE,'message'=>'Tick here to confirm you have read and understood our Terms of use and Privacy policy.'),
                    
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
            $user->newsletter = $this->newsletter;
            $user->encryptPassword();

            if($user->save(false)) {
                return true;
            }
        }
        return false;
    }
}
