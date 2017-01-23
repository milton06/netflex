<?php
namespace NetFlex\MailerBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MailerService
{
	private $mailer;
	private $em;
	private $message;
	
	public function __construct($mailer, EntityManager $em)
	{
		$this->mailer = $mailer;
		$this->em = $em;
	}
	
	public function getMailTemplateData($mailTemplateTypeKey)
	{
		$mailData = $this->em->getRepository('NetFlexMailerBundle:MailTemplate')->findOneByTypeKey($mailTemplateTypeKey);
		if (! $mailData) {
			throw new NotFoundHttpException("Template for mail type $mailTemplateTypeKey was not found");
		}
		return [
			$mailData->getSentFromEmail(),
			$mailData->getSentFromName(),
			$mailData->getSubject(),
			$mailData->getBody(),
		];
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
    
    public function setMessageWithAttachment($fromEmail, $toEmail, $subject, $attachment, $message, $priority = 1, $fromName = null, $toName = null)
    {
        $this->message = \Swift_Message::newInstance()
        ->setFrom($fromEmail, $fromName)
        ->setTo($toEmail, $toName)
        ->setPriority($priority)
        ->setSubject($subject)
        ->attach(\Swift_Attachment::fromPath($attachment))
        ->setBody($message, 'text/html');
    }
	
	public function sendMail()
	{
		$this->mailer->send($this->message);
	}
}
