<?php

namespace NetFlex\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class AuthController extends Controller
{
    /**
     * Authenticate and log user into the dashboard.
     *
     * @Route("/dashboard/login", name="dashboard_login")
     * @Method({"GET", "POST"})
     */
    public function indexAction()
    {
        $username = '';
	    $error = [];
	    
	    $authUtils = $this->get('security.authentication_utils');
	    
	    if (null !== ($authError = $authUtils->getLastAuthenticationError()))
	    {
		    $username = $authError->getToken()->getUsername();
		    $error = $this->getSpecificAuthErrorMessage($username);
	    }
	    
	    return $this->render('NetFlexUserBundle:Auth:login.html.twig', [
	    	'pageTitle' => 'Dashboard Login',
		    'username' => $username,
		    'error' => $error,
	    ]);
    }
	
	/**
	 * Gets a specific error message.
	 *
	 * @param  string $username
	 *
	 * @return array
	 */
    protected function getSpecificAuthErrorMessage($username)
    {
	    $userRepo = $this->getDoctrine()->getManager()->getRepository('NetFlexUserBundle:User');
	    
	    /**
	     * First we'll check if the username exists in the data table.
	     */
	    $user = $userRepo->findOneByUsername($username);
	    
	    if (! $user) {
		    /**
		     * Username was not found in the data table.
		     */
		    return ['_username' => 'This username is not registered with us'];
	    }
	    
	    /**
	     * If username was found, then the password must have been wrong.
	     */
	    return ['_password' => 'Wrong password provided'];
    }
}
