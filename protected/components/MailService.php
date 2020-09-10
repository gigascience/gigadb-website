<?php

/**
 * Service for sending emails
 *
 * @author Peter Li <peter+git@gigasciencejournal.com>
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class MailService extends CApplicationComponent
{
    protected $mailer;

    public function init()
    {
        $this->mailer = Yii::$app->mailer;
        parent::init();
    }

    /**
     * Send email message
     *
     * @param string $from sender email
     * @param string $to recipient email
     * @param string $subject email's subject
     * @param string $content content to send
     * @return bool whether sending the email is successful or not
     */
    public function sendEmailMessage(string $from, string $to, string $subject, string $content)
    {
        return $this->mailer->compose()
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->setTextBody($content)
            ->send();
    }

    /**
     * Send HTML email message
     *
     * @param string $from sender email
     * @param string $to recipient email
     * @param string $subject email's subject
     * @param string $content content to send
     * @return bool whether sending the email is successful or not
     */
    public function sendHTMLEmailMessage(string $from, string $to, string $subject, string $content)
    {
        return $this->mailer->compose('template',
            ['top_img' => '/var/www/images/email/top.png',
                'bottom_img' => '/var/www/images/email/bottom.png',
                'logo_img' => '/var/www/images/email/logo.png',
                'content' => $content])
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->send();
    }
}
?>
