<?php
/**
 * An action for displaying request password page
 */
class ForgotPasswordAction extends CAction
{
    public function run()
    {
        $this->getController()->layout = "new_main";
        $resetPasswordRequestForm = new ResetPasswordRequestForm;
        if (isset($_POST['LostUserPassword'])) {
            $resetPasswordRequestForm->email = $_POST['LostUserPassword']['email'];
            if ($resetPasswordRequestForm->validate()) {
                $user = User::model()->findByAttributes(array('email' => $resetPasswordRequestForm->email));
                if ($user !== null) {
                    Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": Found user account for ".$resetPasswordRequestForm->email, 'info');
                    $this->generateResetToken($user);
                }
                else {
                    Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": User account not found for ".$user, 'info');
                }
            }
            $this->getController()->redirect(array('user/resetThanks'));
        }
        $this->getController()->render('reset');
    }
}
