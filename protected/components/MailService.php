<?php

class MailService extends CApplicationComponent
{
    public $mailer;

    public function init()
    {
        parent::init();
    }

    /**
     * send email message
     *
     * @param string $from sender email
     * @param string $to recipient email
     * @param string $subject email's subject
     * @param string $content content to send
     * @return bool whether sending the email is successful or not
     */
    public function sendEmailMessage(string $from, string $to, string $subject, string $content)
    {
        $log1 = get_class(Yii::app()); // outputs 'CWebApplication'
        $log2 = get_class(Yii::$app);  // outputs 'yii\web\Application'
        Yii::log(__FUNCTION__."MailService > $log1");
        Yii::log(__FUNCTION__."MailService > $log2");
        
        Yii::$app->mailer->compose()
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->setTextBody($content)
            ->send();
    }

}
?>