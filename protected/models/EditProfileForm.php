<?php

class EditProfileForm extends CFormModel
{
	public $first_name;
	public $last_name;
	public $email;
	public $affiliation;
	public $newsletter;
	public $user_id;
	public $preferred_link;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('email, first_name, last_name, newsletter, user_id, affiliation', 'required'),
			array('preferred_link', 'safe'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
		            'email' => Yii::t('app' , 'Email'),
		            'first_name' => Yii::t('app' , 'First Name'),
		            'last_name' => Yii::t('app' , 'Last Name'),
		            'affiliation' => Yii::t('app' , 'Affiliation'),
		            'preferred_link' => Yii::t('app', 'Link out preference'),
		);
	}

    public function updateInfo(){
        $user = User::model()->findByPk($this->user_id);
        if($user){
            $user->first_name = $this->first_name;
            $user->last_name = $this->last_name;
            $user->affiliation = $this->affiliation;
            $user->password_repeat ='NoNeed';
            $user->email = $this->email;
	   $user->previous_newsletter_state = $user->newsletter;
            $user->newsletter = $this->newsletter;
            $user->preferred_link = $this->preferred_link;

            if($user->save()) {
                return true;
            }         
        }
        return false;
    }
}
