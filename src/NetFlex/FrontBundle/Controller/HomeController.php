<?php

namespace NetFlex\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class HomeController extends Controller
{
    /**
     * Renders NetFlex home page.
     *
     * @Route("/", name="home_page")
     * @Method({"GET"})
     *
     * @param  Request  $request A Request instance
     *
     * @return Response          A Response instance
     */
    public function renderHomePageAction(Request $request)
    {
        return $this->render('NetFlexFrontBundle:Home:home.html.twig', [
        	'pageTitle' => 'Home',
        ]);
    }
	
	/**
	 * @Route("/contact", name="contact_netflex")
	 * @Method({"GET", "POST"})
	 *
	 * @param  Request $request
	 *
	 * @return Response|RedirectResponse
	 */
	public function renderContactNetflexFormAction(Request $request)
	{
		$contactForm = $this->createFormBuilder()
			->add('name', TextType::class, [
				'constraints' => [
					new NotBlank([
						'message' => 'Please provide your name',
					]),
				],
			])
			->add('email', EmailType::class, [
				'constraints' => [
					new NotBlank([
						'message' => 'Please provide your email',
					]),
					new Email([
						'message' => 'Please provide a valid email',
					]),
				],
			])
			->add('message', TextareaType::class, [
				'constraints' => [
					new NotBlank([
						'message' => 'Please provide a message',
					]),
					new Length([
						'min' => 20,
						'minMessage' => 'Message should atleast be 20 characters long',
					]),
				],
			])
			->getForm();
		
		$contactForm->handleRequest($request);
		
		if ($contactForm->isSubmitted() && $contactForm->isValid()) {
			$contactData = $contactForm->getData();
			
			/**
			 * Send mail.
			 */
			$mailerService = $this->get('mailer_service');
			list($toEmail, $toName, $subject, $message) = $mailerService->getMailTemplateData('CNTCT_NF');
			$message = $this->renderView('NetFlexMailerBundle::mail_layout.html.twig', [
				'mailBody' => $message,
			]);
			$message = str_replace(['[name]', '[email]', '[message]'], [$contactData['name'], $contactData['email'], $contactData['message']], $message);
			$message = html_entity_decode($message);
			$mailerService->setMessage($contactData['email'], $toEmail, $subject, $message, 1, $contactData['name'], $toName);
			$mailerService->sendMail();
			
			$this->addFlash('success', 'Your mail has been sent');
			
			return $this->redirectToRoute('contact_netflex');
		}
		
		return $this->render('NetFlexFrontBundle:Home:contact_netflex.html.twig', [
			'pageTitle' => 'Contact',
			'pageHeader' => 'Contact Team Netflex',
			'contactForm' => $contactForm->createView(),
		]);
	}
	
	/**
	 * Renders NetFlex about page.
	 *
	 * @Route("/about-us", name="about_us")
	 * @Method({"GET"})
	 *
	 * @param  Request $request A Request instance
	 *
	 * @return Response         A Response instance
	 */
	public function aboutUsAction(Request $request)
	{
		return $this->render('NetFlexFrontBundle:Home:about.html.twig', [
			'pageTitle' => 'About Netflex',
			'pageHeader' => 'About Netflex',
		]);
	}
	
	/**
	 * Renders a NetFlex CMS page.
	 *
	 * @Route("/{cmsPageSlug}", name="cms_page", requirements={"cmsPageSlug": "[a-zA-z0-9_\-]+"})
	 * @Method({"GET"})
	 *
	 * @param  string  $cmsPageSlug
	 * @param  Request $request A Request instance
	 *
	 * @return Response          A Response instance
	 */
	public function renderCMSPageAction($cmsPageSlug, Request $request)
	{
		$cmsPageSlug = trim($cmsPageSlug);
		
		$pageSlugDataMap = [
			'career' => [
				'title' => 'Career With Netflex',
				'pageHeader' => 'Career With Netflex',
			],
			'customer-enquiry' => [
				'title' => 'Customer Enquiry',
				'pageHeader' => 'Customer Enquiry',
			],
			'client' => [
				'title' => 'Netflex Clientele',
				'pageHeader' => 'Netflex Clientele',
			],
			'franchisee' => [
				'title' => 'Netflex Franchisee',
				'pageHeader' => 'Netflex Franchisee',
			],
		];
		
		if (false === array_key_exists($cmsPageSlug, $pageSlugDataMap)) {
			throw $this->createNotFoundException('Page Not Found');
		}
		
		return $this->render('NetFlexFrontBundle:Home:page.html.twig', [
			'pageTitle' => $pageSlugDataMap[$cmsPageSlug]['title'],
			'pageHeader' => $pageSlugDataMap[$cmsPageSlug]['pageHeader'],
		]);
	}
	
	/**
	 * Dummy logging out option.
	 *
	 * @Route("/client/dummy-logout", name="dummy_logout")
	 * @Method({"GET"})
	 *
	 * @param  Request  $request A Request instance
	 *
	 * @return Response          A Response instance
	 */
	public function logUserOutAction(Request $request)
	{
		$session = $request->getSession();
		
		if ($session->has('loggedInUsername')) {
			$session->remove('loggedInUsername');
		}
		
		return $this->redirectToRoute('net_flex_client_logout');
	}
}
