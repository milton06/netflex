<?php

namespace NetFlex\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NetFlex\UserBundle\Entity\Role;
use NetFlex\UserBundle\Entity\Email;
use NetFlex\UserBundle\Entity\Contact;
use NetFlex\UserBundle\Entity\User;
use NetFlex\UserBundle\Form\FrontEndUserRegistrationType;

class ClientRegistrationController extends Controller
{
    /**
     * Renders NetFlex client registration layout. Also handles registration form submission.
     *
     * @Route("/client/register", name="front_end_client_registration")
     * @Method({"GET", "POST"})
     *
     * @param  Request  $request      A Request instance
     *
     * @return Response|JsonResponse  A Response|JsonResponse instance
     */
    public function registerClientAction(Request $request)
    {
	    $em = $this->getDoctrine()->getManager();
	
	    /**
	     * Search for 'ROLE_CLIENT' to assign to this client.
	     */
	    $role = $em->getRepository('NetFlexUserBundle:Role')->findOneBy(['id' => 3, 'status' => 1]);
	    if (! $role) {
		    /**
		     * No role as 'ROLE_CLIENT' found!
		     */
		    throw $this->createNotFoundException('No user role exists for a client!');
	    }
	
	    /**
	     * Create empty Address, Email, Contact and User entities.
	     */
	    $email = new Email();
	    $contact = new Contact();
	    $user = new User();
	
	    /**
	     * Populate the User entity.
	     */
	    $user->addRole($role);
	    $user->addEmail($email);
	    $user->addContact($contact);
	
	    /**
	     * Create the registration form.
	     */
	    $form = $this->createForm(FrontEndUserRegistrationType::class, $user);
	
	    /**
	     * Register client.
	     */
	    $form->handleRequest($request);
	    if ($form->isSubmitted()) {
		    /**
		     * Validate
		     */
		    $errorList = [];
		    $errors = $this->get('validator')->validate($form);
		    if (0 < count($errors)) {
			    foreach ($errors as $error) {
				    $errorList[substr($error->getPropertyPath(), (strrpos($error->getPropertyPath(), '.') + 1))] = $error->getMessage();
			    }
		    }
		    /*$email = $user->getEmails()[0]->getEmail();
		    if (0 < $em->getRepository('NetFlexUserBundle:Email')->findAnExistingUserEmail($email)) {
			    $errorList['email'] = 'This email is already taken';
		    }*/
		    if ($errorList) {
			    return $this->json(['status' => false, 'errorList' => $errorList]);
		    }
		    
		    /**
		     * Populate entities with default values.
		     */
		    $currentDateTime = new \DateTime();
		    $user->setPassword($this->get('security.password_encoder')->encodePassword($user, $user->getPassword()));
		    $user->setStatus(2);
		    $user->setCreatedOn($currentDateTime);
		    $user->setLastModifiedOn($currentDateTime);
		    foreach ($user->getEmails() as $email) {
			    $email->setUserId($user);
			    $email->setIsPrimary(1);
			    $email->setStatus(1);
			    $em->persist($email);
		    }
		    foreach ($user->getContacts() as $contact) {
			    $contact->setUserId($user);
			    $contact->setIsPrimary(1);
			    $contact->setStatus(1);
			    $em->persist($contact);
		    }
		    
		    $em->flush();
		    
		    return $this->json(['status' => true]);
	    }
	    
	    return $this->render('NetFlexFrontBundle:Registration:client_form.html.twig', [
        	'form' => $form->createView(),
        ]);
    }
}
