<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class ContactForm extends CFormModel {
	public $name;
	public $email;
	public $subject;
	public $body;
	public $verifyCode;

    /** For the captcha */
    public $validacion;

	/**
	 * Declares the validation rules.
	 */
	public function rules() {
	
		return array(
			array('name, email, subject, body', 'required'),
			array('email', 'email'),
			array('verifyCode', 'validateCaptcha'),			
		);
	}
	/**
	* Validate captcha
	*/
	public function validateCaptcha($attribute, $params){
		$file = "images/tempcaptcha/".$_SESSION["captcha"].".png";
		
		if (empty($this->$attribute)){			
			//Check if file exist
			if(file_exists($file)){
				//Delete file 				
				 unlink($file);
				 $this->addError($attribute, 'Captcha is required');
			}
		}
		else if (!empty($this->$attribute)){
	      if($this->$attribute == $_SESSION["captcha"]){
	      	//Delete file 				
			unlink($file);			
	      }else{
	      	//Delete file 				
			unlink($file);
	      	$this->addError($attribute, 'Captcha is incorrect!');
	      }
	  	}
	    else{
	    	//	Delete file 				
	    	unlink($file);
	      $this->addError($attribute, 'Captcha is required');
	    }	   
			
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels() {
		return array(
            'validacion' => Yii::t('CAPTCHA', 'Please type the text shown in the image: '),
		);
	}
}
