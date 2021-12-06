<?php

/**
 * User
 * An ActiveRecord model class to handle data related to users of the system.
 */
class User extends CActiveRecord {
    public $password_repeat;
    public $password_new;
    public $terms;
    # Unhashed password for account verification email
    public $passwordUnHashed;

    public $passwordInvalid = false;
    public $sendNewPassword = false;
    public $verifyCode;
    /** For the captcha */
    public $validacion;

    public static $linkouts = array(
            'EBI' => 'EBI',
            'NCBI' => 'NCBI',
            'DDBJ' => 'DDBJ'
    );

    /**
     * Returns the static model of the specified AR class.
     * @return MyActiveRecord the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'gigadb_user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(

            array('email','length','max'=>128),
            array('email', 'required'),
            array('email', 'email'),
            array('email', 'unique'),

            #array('password','length','max'=>128),
            array('password', 'required', 'on'=>'insert'),
            array('password', 'compare', 'compareAttribute'=>'password_repeat', 'on'=>'insert'),
            array('password', 'checkPassword', 'on'=>'update'),
            array('password', 'safe','on'=>'insert'),
            array('password_repeat','required'),
            array('first_name, last_name','length','max'=>60),

            array('first_name','required'),
            array('last_name','required'),
            array('affiliation','required'),
            array('newsletter','boolean'),
            array('terms','required'),
            array('terms','compare', 'on'=>'insert', 'compareValue' => TRUE,'message'=>'Tick here to confirm you have read and understood our Terms of use and Privacy policy.'),
            array('role','safe'),
            array('preferred_link', 'safe'),
            array('verifyCode', 'validateCaptcha'),
        );
    }

    public function checkPassword($attribute, $params) {
        $password = $this->password_new;
        $password_repeat = $this->password_repeat;

        if ($password != '') {
            $password_repeat = $this->password_repeat;

            if ($password != $password_repeat) {
                $this->addError($attribute,"Password and confirm don't match");
                return false;
            }
            else {
                Yii::log(__FUNCTION__."> match", 'debug');
            }

            $this->password = $this->password_new;
        }
        return true;
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
            //  Delete file
            unlink($file);
          $this->addError($attribute, 'Captcha is required');
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array (
            'docs'=>array(self::HAS_MANY, 'Doc', 'author_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'username' => 'Username',
            'terms'=> 'Terms and Conditions',
            'email' => Yii::t('app' , 'Email'),
            'terms'=> 'Terms and Conditions',
            'first_name' => Yii::t('app' , 'First Name'),
            'last_name' => Yii::t('app' , 'Last Name'),
            'password' => Yii::t('app' , 'Password'),
            'affiliation' => Yii::t('app' , 'Affiliation'),
            'password_repeat' => Yii::t('app' ,'Confirm Password'),
        );
    }

    #public function validate($scenario, $attributes) {
    #  $valid = parent::validate($scenario, $attributes);
#
#      if ($scenario == 'insert' && !$this->attributes['password']) {
#        $this->addError("password", "Password cannot be blank");
#        $this->passwordInvalid = true;
#        $valid = false;
#      }
#
#      return $valid;
#    }

    #public function beforeSave() {
    #  // Screw you, MVC
    #  if ($_POST['_noFillPassword'])
    #    $this->password = md5($this->attributes['password']);
#
#      return true;
#    }

    protected function beforeValidate() {
        if ($this->isNewRecord) {
           // $this->created_at = $this->updated_at = date('Y-m-d H:i:s');
           //$this->ip_address = $_SERVER['REMOTE_ADDR'];
        }
        else {
           // $this->updated_at = date('Y-m-d H:i:s');
        }

        return true;
    }

    /**
     * Replace inplace user's password with a hashed version
     *
     * @uses sodium_crypto_pwhash_str()
     * @see https://paragonie.com/book/pecl-libsodium/read/07-password-hashing.md
     */
    public function encryptPassword() {
        # TODO: use salt?
        # if(md5(md5($this->password).$user->salt)!==$user->password)
        #Yii::log(__FUNCTION__."> encryptPassword password before hash = " . $this->password, 'debug');
        $this->password = sodium_crypto_pwhash_str(
                            $this->password,
                            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
                            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
                        );
        #Yii::log(__FUNCTION__."> encryptPassword password after  hash = " . $this->password, 'debug');
    }

    /**
     * Generate a random password securely
     *
     * @param int $length length of the password to generate (default to 8)
     * @return string the generated password
     * @see https://paragonie.com/blog/2015/07/how-safely-generate-random-strings-and-integers-in-php
     * @see https://stackoverflow.com/questions/30145715/why-srandtime-is-a-bad-seed/30146979#30146979
     * @see https://stackoverflow.com/questions/6101956/generating-a-random-password-in-php/31284266#31284266
     * @see https://github.com/jedisct1/libsodium-php/issues/163
     */
    public function generatePassword($length=8) {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        $str = '';
        $keysize = strlen($chars) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $chars[random_int(0, $keysize)];
        }
        return $str;
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('LOWER(email)',strtolower($this->email),true);
        $criteria->compare('LOWER(first_name)',strtolower($this->first_name),true);
        $criteria->compare('LOWER(last_name)',strtolower($this->last_name),true);
        $criteria->compare('LOWER(affiliation)',strtolower($this->affiliation),true);
        $criteria->compare('newsletter',strtolower($this->newsletter));
        $criteria->compare('is_activated',strtolower($this->is_activated));

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>30,
                ),
        ));
    }

    public function renderNewsletter(){
        return $this->newsletter ? 'Yes' : 'No';
    }


    public function getRole()
    {
        $role = Yii::app()->db->createCommand()
                ->select('itemname')
                ->from('AuthAssignment')
                ->where('userid=:id', array(':id'=>$this->id))
                ->queryScalar();

        return $role;
    }

    public function getLinkedAuthor() {
        $criteria = new CDbCriteria;
        $criteria->addColumnCondition(array('t.gigadb_user_id' => $this->id));
        $author = Author::model()->find($criteria);
        return $author;
    }

    /**
    * Return the full name of the user
    *
    *
    * @return string
    */
    public function getFullName() {
        return $this->first_name." ".$this->last_name;
    }
/**
  * process OAuth response after successfull authorisaion and redirection to the loginAffilate callback
  * TODO: the logic with name vs first_name+last_name may not be ideal (eg: my firstname Rija is my twitter name, it becomes last name in gigadb
*/
    public static function processAffiliateUser($auth) 
    {
        $provider = $auth['provider'];
        $uid = $auth['uid'];
        $info = $auth['info'];
        $email = null ;
        if (isset($info['email'])) {
            $email = $info['email'];
        }
        $username = $provider.":".$uid;
        if((isset($info['first_name'])) and (isset($info['last_name']))) {
            $first_name = $info['first_name'];
            $last_name  =$info['last_name'];
        } else if (isset($info['name'])) {
            $name = explode(" ", $info['name']);
            $last_name = array_pop($name);
            $first_name = implode(" ", $name);
        } else {
            $first_name = $provider.":".$uid;
            $last_name = " ";
        }

        $user = null ;
        # check if exist user by email
        if (null != $email) {
            $user = User::findAffiliateEmail($email);
        }
        else
        # if no email
        {
            $user = User::findAffiliateUser($provider,$uid) ;
        }

        if(!$user) {
            $user = new User;
            $user->email = ($email != null ? $email : $uid."@".$provider);
            $user->username = $username;
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->role = 'user';

            if($provider == "Facebook") {
                $user->facebook_id = $uid;
            } else if ($provider == "Twitter") {
                $user->twitter_id = $uid;
            } else if ($provider == "LinkedIn") {
                $user->linkedin_id = $uid;
            } else if ($provider == "Google") {
                $user->google_id = $uid;
            } else if ($provider == "Orcid") {
                $user->orcid_id = $uid;
            }

            #generate some credential data
            $user->password = $user->generatePassword(32);
            $user->encryptPassword();
        } else {
            # still update the uid if user exist, so session and database record still match
            if($provider == "Facebook") {
                $user->facebook_id = $uid;
            } else if ($provider == "Twitter") {
                $user->twitter_id = $uid;
            } else if ($provider == "LinkedIn") {
                $user->linkedin_id = $uid;
            } else if ($provider == "Google") {
                $user->google_id = $uid;
            } else if ($provider == "Orcid") {
                $user->orcid_id = $uid;
            }
        }

        # if login with affiliate provider, activate the user, as they are already trusted third-party verified
         $user->is_activated = true;

        if($user->save(false)){
            return $user;
        }
    }

    public static function findAffiliateUser($provider, $uid) 
    {
        $user = null;
        if($provider == "Facebook") {
           $user = User::model()->find("facebook_id = :uid", array(
                ':uid' => $uid
            ));
        } else if ($provider == "Twitter") {
            $user = User::model()->find("twitter_id = :uid", array(
                ':uid' => $uid
            ));
        } else if ($provider == "LinkedIn") {
            $user = User::model()->find("linkedin_id = :uid", array(
                ':uid' => $uid
            ));
        } else if ($provider == "Google") {
            $user = User::model()->find("google_id = :uid", array(
                ':uid' => $uid
            ));
        } else if ($provider == "Orcid") {
            $user = User::model()->find("orcid_id = :uid", array(
                ':uid' => $uid
            ));
        }

        return $user;
    }

    public static function findAffiliateEmail($email) {
        $user = User::model()->find("email = :email", array(
                ':email' => $email
            ));
        return $user;
    }

}

