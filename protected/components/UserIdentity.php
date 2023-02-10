<?php

/**
 * Class to handle the identity of user and how they authenticate
 *
 * @see https://www.yiiframework.com/doc/guide/1.1/en/topics.auth
 */
class UserIdentity extends CUserIdentity
{
    public $_id;
    const ERROR_USER_NOT_ACTIVATED = 3;

    /**
     * This is the method used to encapsulate the main details of the authentication approach.
     *
     * An identity class may also declare additional identity information that needs
     * to be persistent during the user session. In this case $user->id and $user->role
     *
     * @uses \PasswordHelper::verifyPassword()
     * @return boolean whether authentication succeeds. True if successful, False otherwise
     */
    public function authenticate()
    {
        $user = User::model()->findByAttributes(array('email' => $this->username));

        if ($user === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else {
            if (PasswordHelper::verifyPassword($this->password, $user->password) && $user->is_activated) {
                $this->_id = $user->id;
                $this->errorCode = self::ERROR_NONE;
                $this->setState("_id", $user->id);
                $this->setState("_preferredLink", $user->preferred_link);
                $this->setState('roles', $user->role);
            } elseif (!$user->is_activated) {
                $this->errorCode = self::ERROR_USER_NOT_ACTIVATED;
            } else {
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
            }
        }

        return !$this->errorCode;
    }

    public function getId()
    {
        return $this->_id;
    }
}
