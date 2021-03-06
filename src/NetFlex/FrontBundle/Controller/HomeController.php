<?php

namespace NetFlex\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
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
	 * Renders a NetFlex CMS page.
	 *
	 * @Route("/{cmsPageSlug}", name="cms_page", requirements={"cmsPageSlug": "[a-zA-z0-9_\-]+"})
	 * @Method({"GET", "POST"})
	 *
	 * @param  string  $cmsPageSlug
	 * @param  Request $request  A Request instance
	 *
	 * @return Response          A Response instance
	 */
	public function renderCMSPageAction($cmsPageSlug, Request $request)
	{
		$cmsPageSlug = trim($cmsPageSlug);
		
		$cmsPage = $this->getDoctrine()->getManager()->getRepository('NetFlexStaticPageBundle:StaticPage')->findStaticPageForFrontBySlug($cmsPageSlug);
		
		if (! $cmsPage) {
		    throw $this->createNotFoundException('Static page was not found');
        }
		
		$extraData = [];
		$viewFile = 'NetFlexFrontBundle:Home:page.html.twig';
		
		switch ($cmsPageSlug) {
            case 'franchisee':
                break;
            
            case 'customer-enquiry':
                break;
		    
		    case 'about-us':
                $viewFile = 'NetFlexFrontBundle:Home:about.html.twig';
                
                break;
                
            case 'career':
                $viewFile = 'NetFlexFrontBundle:Home:career.html.twig';
                
                $careerForm = $this->createFormBuilder()
                ->setAction($this->generateUrl('cms_page', ['cmsPageSlug' => 'career'], UrlGeneratorInterface::ABSOLUTE_URL))
                ->setMethod('POST')
                ->add('name', TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Name is required'
                        ]),
                    ],
                ])
                ->add('email', EmailType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Email is required',
                        ]),
                        new Email([
                            'message' => 'Enter a valid email',
                        ]),
                    ],
                ])
                ->add('contact', TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Contact number is required',
                        ]),
                    ],
                ])
                ->add('position', TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Applied position is required',
                        ]),
                    ],
                ])
                ->add('brief', TextareaType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Brief is required',
                        ]),
                    ],
                ])
                ->add('cv', FileType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please upload a CV',
                        ]),
                        new File([
                            'mimeTypes' => [
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ],
                            'mimeTypesMessage' => '{{ types }} files are only allowed',
                            'maxSize' => '5Mi',
                            'maxSizeMessage' => 'Allowed maximum size is {{ limit }} {{ suffix }}',
                        ]),
                    ],
                ])
                ->getForm();
                
                $careerForm->handleRequest($request);
                
                if ($careerForm->isSubmitted() && $careerForm->isValid()) {
                    $applicationData = $careerForm->getData();
                    
                    $name = $applicationData['name'];
                    $email = $applicationData['email'];
                    $contact = $applicationData['contact'];
                    $position = $applicationData['position'];
                    $brief = $applicationData['brief'];
                    $file = $applicationData['cv'];
                    
                    $cvName = 'applicant_cv_' . md5(uniqid()) . '.' . $file->guessExtension();
                    $cvAttachmentPath = $this->getParameter('applicant_cv_upload_directory_path') . '/' . $cvName;
                    
                    $file->move(
                        $this->getParameter('applicant_cv_upload_directory_path'),
                        $cvName
                    );
    
                    $mailerService = $this->get('mailer_service');
                    list($toEmail, $toName, $subject, $message) = $mailerService->getMailTemplateData('CR_APLCTN');
                    $message = $this->renderView('NetFlexMailerBundle::mail_layout.html.twig', [
                        'mailBody' => $message,
                    ]);
                    $message = str_replace(['[name]', '[email]', '[contact]', '[post]', '[brief]'], [$applicationData['name'], $applicationData['email'], $applicationData['contact'], $applicationData['position'], $applicationData['brief']], $message);
                    $message = html_entity_decode($message);
                    $mailerService->setMessageWithAttachment($email, $toEmail, $subject, $cvAttachmentPath, $message, 1, $name, $toName);
                    $mailerService->sendMail();
    
                    $this->addFlash('success', 'Your application has been submitted successfully.');
    
                    return $this->redirectToRoute('cms_page', ['cmsPageSlug' => 'career']);
                }
                
                $extraData = [
                    'careerForm' => $careerForm->createView(),
                ];
                
                break;
            
            case 'client':
                $viewFile = 'NetFlexFrontBundle:Home:client.html.twig';
                
                break;
                
            case 'contact':
                $viewFile = 'NetFlexFrontBundle:Home:contact_netflex.html.twig';
    
                $contactForm = $this->createFormBuilder()
                ->setAction($this->generateUrl('cms_page', ['cmsPageSlug' => 'contact'], UrlGeneratorInterface::ABSOLUTE_URL))
                ->setMethod('POST')
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
        
                    return $this->redirectToRoute('cms_page', ['cmsPageSlug' => 'contact']);
                }
    
                $extraData = [
                    'contactForm' => $contactForm->createView(),
                ];
                
                break;
                
            default:
                break;
        }
		
		return $this->render($viewFile, array_merge(
            [
                'pageTitle' => $cmsPage->getTitle(),
                'pageHeader' => $cmsPage->getTitle(),
                'metaKeyword' => $cmsPage->getMetaKeyword(),
                'metaDescription' => $cmsPage->getMetaDescription(),
                'contents' => $cmsPage->getStaticPageSections(),
            ],
            $extraData
        ));
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
