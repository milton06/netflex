<?php

namespace NetFlex\MailerBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NetFlex\MailerBundle\Entity\MailTemplate;
use NetFlex\MailerBundle\Form\MailTemplateEditForm;

class MailTemplateController extends Controller
{
    /**
     * @Route("/dashboard/mail/template/list", name="list_mail_templates")
     * @Method({"GET"})
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function renderMailTemplateListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
	
	    $breadCrumbs = [
		    [
			    'title' => 'Dashboard Home',
			    'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
		    [
			    'title' => 'Mail Templates',
			    'link' => $this->generateUrl('list_mail_templates', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
	    ];
	    
	    $mailTemplates = $em->getRepository('NetFlexMailerBundle:MailTemplate')->findExistingEmailsForListing();
	    
	    return $this->render('NetFlexMailerBundle:MailTemplate:list.html.twig', [
		    'pageTitle' => 'Mail Template List',
		    'breadCrumbs' => $breadCrumbs,
		    'pageHeader' => '<h1>Mail Template<small> list </small></h1>',
		    'listHeader' => 'Mail Template List',
	    	'mailTemplates' => $mailTemplates,
	    ]);
    }
	
	/**
	 * @Route("/dashboard/mail/template/edit/{id}", name="edit_mail_template", requirements={"id": "\d+"})
	 * @Method({"GET", "POST"})
	 *
	 * @param  MailTemplate             $mailTemplate
	 * @param  Request                  $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function editMailTemplateAction(MailTemplate $mailTemplate, Request $request)
	{
		$form = $this->createForm(MailTemplateEditForm::class, $mailTemplate);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($mailTemplate);
			$em->flush();
			
			$this->addFlash('success', 'Mail template was updated successfully');
			
			return $this->redirectToRoute('edit_mail_template', ['id' => $mailTemplate->getId()]);
		}
		
		$breadCrumbs = [
			[
				'title' => 'Dashboard Home',
				'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
			],
			[
				'title' => 'Mail Templates',
				'link' => $this->generateUrl('list_mail_templates', [], UrlGeneratorInterface::ABSOLUTE_URL),
			],
			[
				'title' => 'Edit Mail Templates',
				'link' => $this->generateUrl('edit_mail_template', ['id' => $mailTemplate->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
			],
		];
		
		return $this->render('NetFlexMailerBundle:MailTemplate:edit.html.twig', [
			'pageTitle' => 'Edit Mail Template',
			'breadCrumbs' => $breadCrumbs,
			'pageHeader' => '<h1>Edit<small> mail template </small></h1>',
			'listHeader' => 'Edit Mail Template',
			'mailTemplate' => $mailTemplate,
			'form' => $form->createView(),
		]);
	}
}
