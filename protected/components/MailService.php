<?php

/**
 * Component service for sending emails
 *
 * @author Peter Li <peter+git@gigasciencejournal.com>
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class MailService extends CApplicationComponent
{
    /** @var yii\swiftmailer\Mailer */
    protected $mailer;

    /**
     * Initialize this component to get new Mailer instance
     */
    public function init()
    {
        $this->mailer = Yii::$app->mailer;
        parent::init();
    }

    /**
     * Send plain text email message
     *
     * @param string $from sender email
     * @param string $to recipient email
     * @param string $subject email's subject
     * @param string $content content to send
     * @return bool whether sending the email is successful or not
     */
    public function sendEmail(string $from, string $to, string $subject, string $content)
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
    public function sendHTMLEmail(string $from, string $to, string $subject, string $content)
    {
        return $this->mailer->compose(
            'template',
            ['top_img' => '/var/www/images/email/top.png',
                'bottom_img' => '/var/www/images/email/bottom.png',
                'logo_img' => '/var/www/images/email/logo.png',
            'content' => $content]
        )
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->send();
    }

    /**
     * Send HTML email message with attachment
     *
     * @param string $from sender email
     * @param string $to recipient email
     * @param string $subject email's subject
     * @param string $content content to send
     * @param string $filepath path to file attachment
     * @param string $attachmentFileName name for the file attachment
     * @return bool whether sending the email is successful or not
     */
    public function sendHTMLEmailWithAttachment(string $from, string $to, string $subject, string $content, string $filepath, string $attachmentFileName)
    {
        return $this->mailer->compose(
            'template',
            ['top_img' => '/var/www/images/email/top.png',
                'bottom_img' => '/var/www/images/email/bottom.png',
                'logo_img' => '/var/www/images/email/logo.png',
            'content' => $content]
        )
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->attach($filepath, array("fileName" => $attachmentFileName))
            ->send();
    }
}
