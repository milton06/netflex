<?php
namespace NetFlex\FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MailControllerTest extends WebTestCase
{
	public function testMailIsSentAndContentIsOk()
	{
		$client = static::createClient();
		
		// Enable the profiler for the next request (it does nothing if the profiler is not available)
		$client->enableProfiler();
		
		$crawler = $client->request('POST', 'front_end_client_registration');
		
		$mailCollector = $client->getProfile()->getCollector('swiftmailer');
		
		// Check that an email was sent
		$this->assertEquals(1, $mailCollector->getMessageCount());
		
		$collectedMessages = $mailCollector->getMessages();
		$message = $collectedMessages[0];
		
		// Asserting email data
		$this->assertInstanceOf('Swift_Message', $message);
		$this->assertEquals('Client registration success mail', $message->getSubject());
		$this->assertEquals('support@nteflex.com', key($message->getFrom()));
		$this->assertEquals('recipient@example.com', key($message->getTo()));
		$this->assertEquals(
			'You should see me from the profiler!',
			$message->getBody()
		);
	}
}
