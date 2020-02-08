<?php

namespace common\components;

/**
 * Service for handling message sending
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class MessagingService extends \yii\base\Component
{

	/** @var yii\mail\MailerInterface $mailer class to send email */
	protected $mailer;

	public function __construct(/*yii\mail\MailerInterface*/ $mailer, $config = [])
	{
		$this->mailer = $mailer;

		parent::__construct($config);
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
	public function sendEmailMessage(string $from, string $to, string $subject, string $content): bool
	{
		return $this->mailer->compose()
		    ->setFrom($from)
		    ->setTo($to)
		    ->setSubject($subject)
		    ->setTextBody($content)
		    ->send();
	}

}
?>