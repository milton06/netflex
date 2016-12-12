<?php
namespace NetFlex\DashboardBundle\Service;

class MailerService
{
	private $mailer;
	private $message;
	
	public function __construct($mailer)
	{
		$this->mailer = $mailer;
	}
	
	public function setMessage($fromEmail, $toEmail, $subject, $message, $priority = 1, $fromName = null, $toName = null)
	{
		$this->message = \Swift_Message::newInstance()
			->setFrom($fromEmail, $fromName)
			->setTo($toEmail, $toName)
			->setPriority($priority)
			->setSubject($subject)
			->setBody($message, 'text/html');
	}
	
	public function sendMail()
	{
		$this->mailer->send($this->message);
	}
}
