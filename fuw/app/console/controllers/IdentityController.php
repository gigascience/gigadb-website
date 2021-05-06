<?php

namespace console\controllers;
use common\models\User;
use Yii;

/**
 * Manage a user identity in FUW for REST authentication
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class IdentityController extends \yii\console\Controller
{

	/**
	 * @var string $username label for the identity */
	public $username = "" ;

	/**
	 * @var string $email email matching one from gigadb_user table in GigaDB */
	public $email = "" ;

	/**
	 * @var string $role user's role in Gigadb: user or admin */
	public $role = "user" ;


	/** 
	* Add a user identity in FUW
	*/
    public function actionAddIdentity()
    {
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->role = $this->role;
        $user->status = User::STATUS_ACTIVE;
        $user->generateAuthKey();
        $user->generatePasswordResetToken();
        $user->setPassword(Yii::$app->security->generateRandomString(16));
        $user->save();
    }

  /** 
	* Remove a user identity in FUW
	*/
    public function actionRemoveIdentity()
    {
     	$user = User::findOne(["email" => $this->email]);
     	if ($user)
     		$user->delete();
    }  

	public function options($actionID)
    {
        // $actionId might be used in subclasses to provide options specific to action id
        return ['color', 'interactive', 'help', 'username', 'email', 'role'];
    }
}
