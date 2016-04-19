<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */

class SuUserIdentity extends CUserIdentity {
    private $_id;
    private $_admin_userid;

    public function authenticate() {
        if ($this->_admin_userid) {
            $admin_record = User::model()->findByAttributes(array('userid'=>$this->_admin_userid));
            if (!$admin_record) {
                Yii::log("su: Admin user not found", 'error');
                $this->errorCode = self::ERROR_USERNAME_INVALID;
                return !$this->errorCode;
            }
        } else {
            Yii::log("su: Admin user not specified", 'error');
            $this->errorCode = self::ERROR_USERNAME_INVALID;
            return !$this->errorCode;
        }

        # Make sure user has rights to run su
        #$user_role = $admin_record->user_role;
        #if ($user_role != 'admin_user') {
        if(!$admin_record->isAdmin()) {
        #if (!Yii::app()->user->checkAccess('admin_user')) {
            Yii::log("su: User not authorized: " . $admin_record->userid, 'error');
            $this->errorCode = self::ERROR_USERNAME_INVALID;
            return !$this->errorCode;
        }

        $record = User::model()->findByAttributes(array('userid'=>$this->username));

        if ($record===null) {
            Yii::log("su: Target user not found", 'error');
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        }
        else if ($admin_record->password !== md5($this->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        }
        else {
            Yii::log("su: Login admin user '" . $admin_record->email . 
                    "', target user '" . $record->email . "'", 'warning');
            #$this->_id = $record->id;
            #$this->_id = $record->email;
            $this->_id = $record->userid;
            $this->setState("_id", $record->id);
            $this->setState("alias", $record->alias);
            $this->errorCode = self::ERROR_NONE;
        }
        return !$this->errorCode;
    }

    #public function getId() {
    #    return $this->_id;
    #}

    public function setAdminUserid($userid) {
        $this->_admin_userid = $userid;
    }

    public function getName() {
        return $this->getState('alias');
    }
}
