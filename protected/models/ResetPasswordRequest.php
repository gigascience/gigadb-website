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
class ResetPasswordRequest extends CActiveRecord {

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

    /**
     * Retrieves time when reset password was requested
     * @return string
     */
    public function getRequestedAt()
    {
        return $this->requested_at;
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
     * Retrieves time when reset password request expires
     * @return string
     */
    public function getExpiresAt()
    {
        return $this->expires_at;
    }

    /**
     * Returns hashed token
     * @return string
     */
    public function getHashedToken()
    {
        return $this->hashed_token;
    }

    /**
     * Returns selector
     * @return string
     */
    public function getSelector()
    {
        return $this->selector;
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
     * Returns public token
     * @return string
     */
    public function getToken()
    {
        return $this->selector.$this->verifier;
    }
}
