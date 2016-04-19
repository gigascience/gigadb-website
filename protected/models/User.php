<?php

class User extends CActiveRecord {
    public $password_repeat;
    public $password_new;

    # Unhashed password for account verification email
    public $passwordUnHashed;

    public $passwordInvalid = false;
    public $sendNewPassword = false;

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
            array('password', 'unsafe'),
            array('first_name, last_name','length','max'=>60),

            array('first_name','required'),
            array('last_name','required'),
            array('affiliation','required'),
            array('newsletter','boolean'),
            array('newsletter','required'),
            array('role','safe'),
            array('preferred_link', 'safe'),

            array('validacion',
               'application.extensions.recaptcha.EReCaptchaValidator',
               'privateKey'=>Yii::app()->params['recaptcha_privatekey'], 'on'=>'insert'),
        );
    }

    public function checkPassword($attribute, $params) {
        $password = $this->password_new;
        $password_repeat = $this->password_repeat;

        if ($password != '') {
            $password_repeat = $this->password_repeat;

            if ($password != $password_repeat) {
                $this->addError('password',"Password and confirm don't match");
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
            'email' => Yii::t('app' , 'Email'),
            'first_name' => Yii::t('app' , 'First Name'),
            'last_name' => Yii::t('app' , 'Last Name'),
            'password' => Yii::t('app' , 'Password'),
            'affiliation' => Yii::t('app' , 'Affiliation'),
            'password_repeat' => Yii::t('app' ,'Confirm Password'),
            'validacion' => Yii::t('CAPTCHA', 'Enter both words separated by a space: '),
            'preferred_link' => Yii::t('app', 'Link out preference'),
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

    public function encryptPassword() {
        # TODO: use salt?
        # if(md5(md5($this->password).$user->salt)!==$user->password)
        #Yii::log(__FUNCTION__."> encryptPassword password before hash = " . $this->password, 'debug');
        $this->passwordUnHashed = $this->password;
        $this->password = md5($this->password);
        #Yii::log(__FUNCTION__."> encryptPassword password after  hash = " . $this->password, 'debug');
    }

    public function generatePassword($length=8) {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand((double)microtime()*1000000);
        $i = 0;
        $pass = '' ;

        while ($i <= $length) {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $pass .= $tmp;
            $i++;
        }
        return $pass;
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

    public function processAffiliateUser($auth) 
    {
        $provider = $auth['provider'];
        $uid = $auth['uid'];
        $info = $auth['info'];

        $email = $provider.":".$uid;
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

        #check if exist user
        $user = User::findAffiliateUser($provider, $uid);

        if(!$user) {
            $user = new User;
            $user->email = $email;
            $user->username = $username;
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->role = 'user';
            $user->affiliation = $provider;

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
            $user->password = self::generatePassword(32);
            $user->encryptPassword();
        }

        # if login with fb, activate the user
         $user->is_activated = true;

        if($user->save(false)){
            return $user;
        }
    }

    public function findAffiliateUser($provider, $uid) 
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

}

