<?php

namespace NetFlex\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ClientAuthController extends Controller
{
	/**
	 * Authenticate and log client into his profile.
	 *
	 * @Route("/client/login", name="client_login")
	 * @Method({"GET", "POST"})
	 */
	public function authenticateClientAction()
	{
		$username = '';
		$error = [];
		
		$authUtils = $this->get('security.authentication_utils');
		
		if (null !== ($authError = $authUtils->getLastAuthenticationError()))
		{
			$username = $authError->getToken()->getUsername();
			$error = $this->getSpecificAuthErrorMessage($username);
		}
		
		return $this->render('NetFlexFrontBundle:Auth:client_login.html.twig', [
			'username' => $username,
			'clientLoginError' => $error,
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
		 * Now we'll check the status of the user.
		 */
		if (1 != $user->getStatus()) {
			/**
			 * User is not active anymore.
			 */
			return ['_username' => 'Your profile is not active'];
		}
		
		/**
		 * If we've come so far, then the password must have been wrong.
		 */
		return ['_password' => 'Wrong password provided'];
	}
}
