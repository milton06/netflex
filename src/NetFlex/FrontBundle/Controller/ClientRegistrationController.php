<?php

namespace NetFlex\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NetFlex\UserBundle\Entity\Email;
use NetFlex\UserBundle\Entity\Contact;
use NetFlex\UserBundle\Entity\Role;
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
     * @param  Request  $request A Request instance
     *
     * @return Response          A Response instance
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
	     * Create empty Email, Contact and User entities.
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
	    
	    return $this->render('NetFlexFrontBundle:Registration:client_form.html.twig', [
        	'form' => $form->createView(),
        ]);
    }
}
