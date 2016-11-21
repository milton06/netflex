<?php

namespace NetFlex\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NetFlex\LocationBundle\Entity\Country;
use NetFlex\LocationBundle\Entity\State;
use NetFlex\LocationBundle\Entity\City;
use NetFlex\UserBundle\Entity\AddressType;
use NetFlex\UserBundle\Entity\Address;
use NetFlex\UserBundle\Entity\Contact;
use NetFlex\UserBundle\Entity\Email;
use NetFlex\UserBundle\Entity\User;
use NetFlex\UserBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/")
 */
class UserController extends Controller
{
	/**
	 * Renders the client list page.
	 *
	 * @Route("/dashboard/client/list", name="client_list")
	 * @Method({"GET"})
	 *
	 * @param  Request $request
	 *
	 * @return Response A response instance
	 */
	public function renderClientListAction(Request $request)
	{
		$breadCrumbs = [
			[
				'title' => 'Dashboard Home',
				'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
			],
			[
				'title' => 'Client List',
				'link' => $this->generateUrl('client_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
			],
		];
		
		return $this->render('NetFlexUserBundle:Client:list.html.twig', [
			'pageTitle' => 'Client List',
			'breadCrumbs' => $breadCrumbs,
			'pageHeader' => '<h1>Client <small>list </small></h1>',
			'listHeader' => 'Client List',
		]);
	}
	
	/**
	 * Renders the client registration page in the dashboard.
	 * Also handles client registration in the dashboard.
	 *
	 * @Route("/dashboard/client/register", name="register_client_from_dashboard")
	 * @Method({"GET", "POST"})
	 *
	 * @param  Request $request A request instance
	 *
	 * @return Response         A response instance
	 */
	public function registerClientFromDashboardAction(Request $request)
    {
	    $em = $this->getDoctrine()->getManager();
	
	    /**
	     * Repositories.
	     */
	    $roleRepo = $em->getRepository('NetFlexUserBundle:Role');
	    $countryRepo = $em->getRepository('NetFlexLocationBundle:Country');
	    $stateRepo = $em->getRepository('NetFlexLocationBundle:State');
	    $cityRepo = $em->getRepository('NetFlexLocationBundle:City');
	    $addressTypeRepo = $em->getRepository('NetFlexUserBundle:AddressType');
	
	    /**
	     * Default values.
	     */
	    $clientRole = $roleRepo->findOneBy(['id' => 3, 'status' => 1]);
	    $defaultCountry = $countryRepo->findOneBy(['id' => 1, 'status' => 1]);
	    $defaultState = $stateRepo->findOneBy(['id' => 41, 'status' => 1]);
	    $defaultCity = $cityRepo->findOneBy(['id' => 5583, 'status' => 1]);
	    $defaultAddressType = $addressTypeRepo->findOneBy(['id' => 1, 'status' => 1]);
	
	    /**
	     * User entity components.
	     */
	    $address = new Address();
	    $contact = new Contact();
	    $email = new Email();
	    $user = new User();
	
	    /**
	     * Set default values.
	     */
	    $user->addRole($clientRole);
	    $address->setCountryId($defaultCountry);
	    $address->setStateId($defaultState);
	    $address->setCityId($defaultCity);
	    $address->setAddressTypeId($defaultAddressType);
	    $user->addAddress($address);
	    $user->addContact($contact);
	    $user->addEmail($email);
	    
	    $registerClientForm = $this->createForm(UserType::class, $user);
	    
	    $registerClientForm->handleRequest($request);
	    
	    if ($registerClientForm->isSubmitted() && $registerClientForm->isValid()) {
		    // TODO: handle client registration
	    }
	
	    $breadCrumbs = [
		    [
			    'title' => 'Dashboard Home',
			    'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
		    [
			    'title' => 'Client List',
			    'link' => $this->generateUrl('client_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
		    [
			    'title' => 'Register New Client',
			    'link' => $this->generateUrl('register_client_from_dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL)
		    ],
	    ];
	    
	    return $this->render('NetFlexUserBundle:Client:register_client_from_dashboard.html.twig', [
		    'pageTitle' => 'Register New Client',
		    'breadCrumbs' => $breadCrumbs,
		    'pageHeader' => '<h1>Register <small>new client </small></h1>',
		    'registerClientForm' => $registerClientForm->createView(),
	    ]);
    }
}
