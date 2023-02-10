<?php

class WebUser extends CWebUser
{
    private $_model;
    /**
     * Overrides a Yii method that is used for roles in controllers (accessRules).
     *
     * "NOTICE: PHP message: PHP Fatal error:  Declaration of WebUser::checkAccess() must be compatible
     * with IWebUser::checkAccess($operation, $params = Array) in /var/www/protected/components/WebUser.php on line 3"
     *
     * @param string $operation Name of the operation required (here, a role).
     * @param mixed $params (opt) Parameters for this operation, usually the object to access.
     * @uses IWebUser::checkAccess
    */
    public function checkAccess($operation, $params = array(), $allowCaching = true)
    {
        if (empty($this->id)) {
            // Not identified => no rights
            return false;
        }
        $role = $this->getState("roles");
        if ($role === 'admin') {
             return true; // admin role has access to everything
        }
             // allow access if the operation request is the current user's role
        return ($operation === $role);
    }

    function getFirst_Name()
    {
        $user = $this->loadUser(Yii::app()->user->id);
        return $user->first_name;
    }

    function getEmail()
    {
        $user = $this->loadUser(Yii::app()->user->id);
        return $user->email;
    }

    protected function loadUser($id = null)
    {
        if ($this->_model === null) {
            if ($id !== null) {
                $this->_model = User::model()->findByPk($id);
            }
        }
        return $this->_model;
    }
}
