<?php

/**
 * This is the model class for table "reset_password_request".
 *
 * The followings are the available columns in table 'reset_password_request':
 * @property string $selector
 * @property string $verifier
 * @property string $hashed_token
 * @property string $requested_at
 * @property string $expires_at
 * @property string $gigadb_user_id
 *
 * The followings are the available model relations:
// * @property User[] $users
 */
class ResetPasswordRequest extends CActiveRecord 
{
    public $verifier;

    public function rules()
    {
        return array(
            array('selector', 'required'),
        );
    }
    
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ResetPasswordRequest the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'reset_password_request';
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
//        return array(
//            'users' => array(self::BELONGS_TO, 'User', 'id')
//        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            '$selector' => 'Selector',
            'requested_at' => 'Request Date',
            'expired_at' => 'Expired Date',
            'gigadb_user_id' => 'GigaDB User Id',
        );
    }

    public function beforeSave()
    {
        $now = new Datetime();
        if(!$this->requested_at)
            $this->requested_at = $now->format(DateTime::ISO8601);
        
        if(!$this->expires_at)
            $this->expires_at = $now->modify('+ 1 hour')->format(DateTime::ISO8601);
        
        parent::beforeSave();
        return true;
    }

    /**
     * Returns true or false depending if reset password request has expired
     * @return bool
     */
    public function isExpired()
    {
        return $this->expires_at <= time();
    }

    /**
     * Returns verifier
     * @return string
     */
    public function getVerifier()
    {
        return $this->verifier;
    }

    /**
     * Returns verifier
     * @return string
     */
    public function setVerifier($verifier)
    {
        $this->verifier = $verifier;
    }

    /**
     * Returns public token
     * @return string
     */
    public function getToken()
    {
        return $this->selector.$this->verifier;
    }

    public static function findResetPasswordRequestBySelector($selector)
    {
        return ResetPasswordRequest::model()->findByAttributes(array('selector' => $selector));
    }

    /**
     * For deleting all previous password reset requests belonging to a user
     * 
     * @param $gigadb_user_id
     * @return void
     */
    public static function deletePasswordRequestsByGigadbUserId($gigadb_user_id)
    {
        $resetPasswordRequests = ResetPasswordRequest::model()->findByAttributes(array('gigadb_user_id' => $gigadb_user_id));
        foreach ($resetPasswordRequests as &$resetPasswordRequest) {
            $resetPasswordRequest->delete();
        }
    }
}
