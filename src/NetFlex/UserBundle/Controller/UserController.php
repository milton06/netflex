<?php

namespace NetFlex\UserBundle\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
	 * @Route("/dashboard/client/list/{page}/{sortColumn}/{sortOrder}", name="client_list", defaults={"page": 1, "sortColumn": "id", "sortOrder": "desc"}, requirements={"page": "\d+", "sortColumn": "id|username", "sortOrder": "asc|desc"})
	 * @Method({"GET", "POST"})
	 *
	 * @param  Request $request
	 *
	 * @return Response A response instance
	 */
	public function renderClientListAction($page, $sortColumn, $sortOrder, Request $request)
	{
		$clientRepo = $this->getDoctrine()->getManager()->getRepository('NetFlexUserBundle:User');
		$roleRepo = $this->getDoctrine()->getManager()->getRepository('NetFlexUserBundle:Role');
		
		$session = $request->getSession();
		$paginationService = $this->get('pagination_service');
		
		$routeParameters = [
			'page' => $page,
			'sortColumn' => $sortColumn,
			'sortOrder' => $sortOrder,
		];
		$routeExtraParameters = $request->query->all();
		
		$clientPaginationParams = $paginationService->getPaginationParameterValue('dashboard.client_list');
		
		$limit = $clientPaginationParams['record_per_page'];
		$neighbor = $clientPaginationParams['neighbor'];
		$offset = $paginationService->getRecordOffset($page, $limit);
		
		$sortColumn = $this->getSortColumn($sortColumn);
		$sortOrder = strtoupper($sortOrder);
		
		$clientName = (true === $session->has('clientName')) ? $session->get('clientName') : '';
		$clientStatus = (true === $session->has('clientStatus')) ? $session->get('clientStatus') : '';
		
		$searchForm = $this->createFormBuilder()
			->setAction($this->generateUrl('client_list', [], UrlGeneratorInterface::ABSOLUTE_URL))
			->setMethod('POST')
			->add('clientName', TextType::class, [
				'data' => $clientName,
			])
			->add('clientStatus', ChoiceType::class, [
				'placeholder' => '-All-',
				'choices' => $this->getParameter('user_statuses'),
				'data' => $clientStatus,
			])
			->getForm();
		
		$searchForm->handleRequest($request);
		
		if (true === $searchForm->isSubmitted()) {
			$searchData = $searchForm->getData();
			
			$session->set('clientName', $searchData['clientName']);
			$session->set('clientStatus', $searchData['clientStatus']);
			
			return $this->redirectToRoute('client_list', array_merge($routeParameters, $routeExtraParameters));
		}
		
		$clientRole = $roleRepo->findOneBy(['id' => 3, 'status' => 1]);
		
		$clients = $clientRepo->findUsers($sortColumn, $sortOrder, $clientName, $clientStatus);
		
		$clientCount = count($clients);
		
		$totalPageCount = $paginationService->getTotalPageCount($limit, $clientCount);
		
		$clients = $clientRepo->findUsers($sortColumn, $sortOrder, $clientName, $clientStatus, $offset, $limit);
		
		$pageLinks = $paginationService->getPageLinks($page, $limit, $neighbor, $clientCount, $totalPageCount, 'client_list', $routeParameters, $routeExtraParameters);
		
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
			'searchForm' => $searchForm->createView(),
			'clientCount' => $clientCount,
			'totalPageCount' => $totalPageCount,
			'clients' => $clients,
			'pageLinks' => $pageLinks,
			'referrer' => urlencode($this->generateUrl('client_list', array_merge($routeParameters, $routeExtraParameters), UrlGeneratorInterface::ABSOLUTE_URL)),
			'allRouteParameters' => array_merge($routeParameters, $routeExtraParameters),
		]);
	}
	
	/**
	 * Gets the current sort column
	 *
	 * @param  string $sortColumn
	 *
	 * @return string
	 */
	private function getSortColumn($sortColumn)
	{
		switch ($sortColumn) {
			case 'username':
				$sortColumn = 'U.username';
				
				break;
			
			case 'id':
			default:
				$sortColumn = 'U.id';
				
				break;
		}
		
		return $sortColumn;
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
	    /**
	     * User entity and related entities; empty.
	     */
	    $address = new Address();
	    $contact = new Contact();
	    $email = new Email();
	    $user = new User();
	
	    /**
	     * Set default values.
	     */
	    $user->addAddress($address);
	    $user->addContact($contact);
	    $user->addEmail($email);
	    
	    $registerClientForm = $this->createForm(UserType::class, $user);
	    
	    $registerClientForm->handleRequest($request);
	    
	    
	    if ($registerClientForm->isSubmitted() && $registerClientForm->isValid()) {
		    $em = $this->getDoctrine()->getManager();
		    
		    $roleRepo = $em->getRepository('NetFlexUserBundle:Role');
		
		    /**
		     * Set role to client.
		     */
		    $clientRole = $roleRepo->findOneBy(['id' => 3, 'status' => 1]);
		    $user->addRole($clientRole);
		
		    /**
		     * Set default values.
		     */
		    $user->setStatus(1);
		    $thisDateTime = new \DateTime();
		    $user->setCreatedOn($thisDateTime);
		    $user->setCreatedBy($this->getUser()->getId());
		    $user->setLastModifiedOn($thisDateTime);
		    $user->setLastModifiedBy($this->getUser()->getId());
		
		    /**
		     * Set encoded password.
		     */
		    $plainTextPassword = $user->getPassword();
		    $user->setPassword($this->get('security.password_encoder')->encodePassword($user, $plainTextPassword));
		    
		    $addresses = $user->getAddresses();
		    $emails = $user->getEmails();
		    $contacts = $user->getContacts();
		    
		    foreach ($addresses as $thisAddress) {
			    $thisAddress->setStatus(1);
			    
			    $em->persist($thisAddress);
		    }
		    
		    foreach ($emails as $thisEmail) {
			    $thisEmail->setStatus(1);
			    
			    $em->persist($thisEmail);
		    }
		    
		    foreach ($contacts as $thisContact) {
			    $thisContact->setStatus(1);
			    
			    $em->persist($thisContact);
		    }
		    
		    $em->flush();
		
		    /**
		     * Send mail.
		     */
		    $mailerService = $this->get('mailer_service');
		    list($fromEmail, $fromName, $subject, $message) = $mailerService->getMailTemplateData('CRT_CLNT');
		    $message = $this->renderView('NetFlexMailerBundle::mail_layout.html.twig', [
			    'mailBody' => $message,
		    ]);
		    $message = str_replace(['[clientName]', '[username]', '[password]'], [$user->getFirstName() . ' ' . $user->getLastName(), $user->getUsername(), $plainTextPassword], $message);
		    $message = html_entity_decode($message);
		    $mailerService->setMessage($fromEmail, $email->getEmail(), $subject, $message, 1, $fromName, $user->getFirstName() . ' ' . $user->getLastName());
		    $mailerService->sendMail();
		    
		    $userId = $user->getId();
		
		    $this->addFlash('success', 'Client has been registered successfully');
		    
		    return $this->redirectToRoute('edit_client_profile_from_dashboard', ['userId' => $userId]);
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
	
	    $referrer = urldecode($request->query->get('ref'));
	    
	    return $this->render('NetFlexUserBundle:Client:register_client_from_dashboard.html.twig', [
		    'pageTitle' => 'Register New Client',
		    'breadCrumbs' => $breadCrumbs,
		    'referrer' => $referrer,
		    'pageHeader' => '<h1>Register <small>new client </small></h1>',
		    'registerClientForm' => $registerClientForm->createView(),
	    ]);
    }
	
	/**
	 * Renders a client profile edit page in the dashboard.
	 * Also handles client profile update in the dashboard.
	 *
	 * @Route("/dashboard/client/edit/{userId}", name="edit_client_profile_from_dashboard", requirements={"userId": "\d+"})
	 * @Method({"GET", "POST"})
	 *
	 * @param  int    $userId
	 * @param  Request $request A request instance
	 *
	 * @return Response         A response instance
	 */
	public function editClientProfileFromDashboard($userId, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		
		$userRepo = $em->getRepository('NetFlexUserBundle:User');
		
		/**
		 * Find active user by ID.
		 */
		$user = $userRepo->findOneBy(['id' => $userId, 'status' => [1, 2]]);
		
		if (! $user) {
			throw $this->createNotFoundException("No such user with ID: $userId exists");
		}
		
		/**
		 * Remove inactive addresses.
		 */
		$userAddresses = $user->getAddresses();
		foreach ($userAddresses as $thisAddress) {
			if (! $thisAddress->getStatus()) {
				$user->removeAddress($thisAddress);
			}
		}
		
		/**
		 * Remove inactive emails.
		 */
		$userEmails = $user->getEmails();
		foreach ($userEmails as $thisEmail) {
			if (! $thisEmail->getStatus()) {
				$user->removeEmail($thisEmail);
			}
		}
		
		/**
		 * Remove inactive contacts.
		 */
		$userContacts = $user->getContacts();
		foreach ($userContacts as $thisContact) {
			if (! $thisContact->getStatus()) {
				$user->removeContact($thisContact);
			}
		}
		
		$editClientForm = $this->createForm(UserType::class, $user);
		
		$editClientForm->handleRequest($request);
		
		if ($editClientForm->isSubmitted() && $editClientForm->isValid()) {
			if ($user->getPassword()) {
				/**
				 * Password provided. Encode it.
				 */
				$user->setPassword($this->get('security.password_encoder')->encodePassword($user, $user->getPassword()));
			} else {
				/**
				 * Password was not provided. Populate entity field with old encoded password from the data table.
				 */
				$user->setPassword($userRepo->findUserEncryptedPassword($userId));
			}
			
			/**
			 * Set default data.
			 */
			$thisDateTime = new \DateTime();
			$user->setLastModifiedOn($thisDateTime);
			$user->setLastModifiedBy($this->getUser()->getId());
			
			$addresses = $user->getAddresses();
			$emails = $user->getEmails();
			$contacts = $user->getContacts();
			
			foreach ($addresses as $thisAddress) {
				if (! $thisAddress->getStatus()) {
					if (! $thisAddress->getId()) {
						/**
						 * New address. Set status to active.
						 */
						$thisAddress->setStatus(1);
						$em->persist($thisAddress);
					} else {
						/**
						 * Old address made inactive.
						 */
						$em->remove($thisAddress);
					}
				}
			}
			
			foreach ($emails as $thisEmail) {
				if (! $thisEmail->getStatus()) {
					if (! $thisEmail->getId()) {
						/**
						 * New email. Set status to active.
						 */
						$thisEmail->setStatus(1);
						$em->persist($thisEmail);
					} else {
						/**
						 * Old email made inactive.
						 */
						$em->remove($thisEmail);
					}
				}
			}
			
			foreach ($contacts as $thisContact) {
				if (! $thisContact->getStatus()) {
					if (! $thisContact->getId()) {
						/**
						 * New contact. Set status to active.
						 */
						$thisContact->setStatus(1);
						$em->persist($thisContact);
					} else {
						/**
						 * Old contact made inactive.
						 */
						$em->remove($thisContact);
					}
				}
			}
			
			$em->flush();
			
			$this->addFlash('success', 'Client profile has been updated successfully');
			
			return $this->redirectToRoute('edit_client_profile_from_dashboard', ['userId' => $userId]);
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
				'title' => 'Edit Client Profile',
				'link' => $this->generateUrl('edit_client_profile_from_dashboard', ['userId' => $userId], UrlGeneratorInterface::ABSOLUTE_URL)
			],
		];
		
		$referrer = urldecode($request->query->get('ref'));
		
		return $this->render('NetFlexUserBundle:Client:edit_client_profile_from_dashboard.html.twig', [
			'pageTitle' => 'Edit Client Profile',
			'breadCrumbs' => $breadCrumbs,
			'referrer' => $referrer,
			'pageHeader' => '<h1>Edit <small>client profile</small></h1>',
			'registerClientForm' => $editClientForm->createView(),
		]);
	}
	
	/**
	 * Renders a client profile view page in the dashboard.
	 *
	 * @Route("/dashboard/client/view/{userId}", name="view_client_profile_in_dashboard", requirements={"userId": "\d+"})
	 * @Method({"GET"})
	 *
	 * @param  int    $userId
	 * @param  Request $request A request instance
	 *
	 * @return Response         A response instance
	 */
	public function viewClientProfileInDashboard($userId, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		
		$userRepo = $em->getRepository('NetFlexUserBundle:User');
		
		/**
		 * Find active user by ID.
		 */
		$user = $userRepo->findOneBy(['id' => $userId, 'status' => [1, 2]]);
		
		if (! $user) {
			throw $this->createNotFoundException("No such user with ID: $userId exists");
		}
		
		/**
		 * Remove inactive addresses.
		 */
		$userAddresses = $user->getAddresses();
		foreach ($userAddresses as $thisAddress) {
			if (! $thisAddress->getStatus()) {
				$user->removeAddress($thisAddress);
			}
		}
		
		/**
		 * Remove inactive emails.
		 */
		$userEmails = $user->getEmails();
		foreach ($userEmails as $thisEmail) {
			if (! $thisEmail->getStatus()) {
				$user->removeEmail($thisEmail);
			}
		}
		
		/**
		 * Remove inactive contacts.
		 */
		$userContacts = $user->getContacts();
		foreach ($userContacts as $thisContact) {
			if (! $thisContact->getStatus()) {
				$user->removeContact($thisContact);
			}
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
				'title' => 'View Client Profile',
				'link' => $this->generateUrl('view_client_profile_in_dashboard', ['userId' => $userId], UrlGeneratorInterface::ABSOLUTE_URL)
			],
		];
		
		$referrer = urldecode($request->query->get('ref'));
		
		return $this->render('NetFlexUserBundle:Client:view_client_profile_in_dashboard.html.twig', [
			'pageTitle' => 'View Client Profile',
			'breadCrumbs' => $breadCrumbs,
			'referrer' => $referrer,
			'pageHeader' => '<h1>Edit <small>client profile</small></h1>',
			'user' => $user,
		]);
	}
	
	/**
	 * Gets all the states of a country.
	 *
	 * @Route("/dashboard/location/state-list", name="list_states_of_a_country")
	 * @Method({"POST"})
	 *
	 * @param  Request $request
	 *
	 * @return JsonResponse     A JsonResponse instance
	 */
    public function listStatesOfACountryAction(Request $request)
    {
	    $stateList = [];
	    $cityList = [];
	
	    $countryId = $request->request->get('countryId');
	
	    if (empty($countryId)) {
		    return $this->json(['stateList' => $stateList, 'cityList' => $cityList]);
	    }
	
	    $countryRepo = $this->getDoctrine()->getManager()->getRepository('NetFlexLocationBundle:Country');
	
	    $states = $countryRepo->findOneById($countryId)->getStates();
	    
	    if (empty($states)) {
		    return $this->json(['stateList' => $stateList, 'cityList' => $cityList]);
	    }
	    
	    $cities = $states[0]->getCities();
	
	    foreach ($states as $thisState) {
		    if (in_array($thisState->getId(), [42, 43, 44, 45, 46, 47])) {
			    continue;
		    }
		    $stateList[$thisState->getId()] = $thisState->getName();
	    }
	    
	    if (empty($cities)) {
		    return $this->json(['stateList' => $stateList, 'cityList' => $cityList]);
	    }
	
	    foreach ($cities as $thisCity) {
		    $cityList[$thisCity->getId()] = $thisCity->getName();
	    }
	
	    return $this->json(['stateList' => $stateList, 'cityList' => $cityList]);
    }
	
	/**
	 * Gets all the cities of a state.
	 *
	 * @Route("/dashboard/location/city-list", name="list_cities_of_a_state")
	 * @Method({"POST"})
	 *
	 * @param  Request $request
	 *
	 * @return JsonResponse     A JsonResponse instance
	 */
	public function listCitiesOfAStateAction(Request $request)
	{
		$cityList = [];
		
		$stateId = $request->request->get('stateId');
		
		if (empty($stateId)) {
			return $this->json(['cityList' => $cityList]);
		}
		
		$stateRepo = $this->getDoctrine()->getManager()->getRepository('NetFlexLocationBundle:State');
		
		$cities = $stateRepo->findOneById($stateId)->getCities();
		
		if (empty($cities)) {
			return $this->json(['cityList' => $cityList]);
		}
		
		foreach ($cities as $thisCity) {
			$cityList[$thisCity->getId()] = $thisCity->getName();
		}
		
		return $this->json(['cityList' => $cityList]);
	}
	
	/**
	 * Deletes a client.
	 *
	 * @Route("/dashboard/client/delete/{mode}", name="delete_client", requirements={"mode": "async|sync"})
	 * @Method({"GET", "DELETE"})
	 *
	 * @param string  $mode    Asynchronous or synchronous operation
	 * @param Request $request A request instance
	 *
	 * @return JsonResponse|RedirectResponse
	 */
	public function deleteClientAction($mode, Request $request)
	{
		$clientId = $request->query->get('client_id');
		
		switch ($mode) {
			case 'async':
				break;
			
			case 'sync':
				$allRouteParameters = $request->query->get('allRouteParameters');
				$pageIndex = (int) $allRouteParameters['page'];
				$selectedRecordCount = (int) $request->query->get('selectedRecordCount');
				$totalRecordOnPage = (int) $request->query->get('totalRecordOnPage');
				
				if (false === is_numeric($clientId)) {
					$clientIds = explode('-', $clientId);
					$undeletedClientIds = [];
					
					foreach ($clientIds as $thisClientId) {
						$clientDeletionResult = $this->deleteClient((int) $thisClientId);
						
						if (false === $clientDeletionResult['status']) {
							$undeletedClientIds[] = $thisClientId;
						}
					}
					
					if (empty($undeletedClientIds)) {
						$this->addFlash('success', 'All clients were deleted successfully');
					} elseif (count($undeletedClientIds) < count($clientIds)) {
						$this->addFlash('warning', 'Clients with ID: ' . implode(', ', $undeletedClientIds) . ' could not be deleted');
					} else {
						$this->addFlash('error', 'Clients could not be deleted');
					}
					
					$allRouteParameters['page'] = $this->adjustPageIndex($pageIndex, ($selectedRecordCount - count($undeletedClientIds)), $totalRecordOnPage);
				} else {
					$clientDeletionResult = $this->deleteClient((int) $clientId);
					
					if (false === $clientDeletionResult['status']) {
						$this->addFlash('error', 'Client could not be deleted');
					} else {
						$this->addFlash('success', 'Client was deleted successfully');
					}
					
					$allRouteParameters['page'] = $this->adjustPageIndex($pageIndex, $selectedRecordCount, $totalRecordOnPage);
				}
				
				return $this->redirectToRoute('client_list', $allRouteParameters);
				
				break;
			
			default:
				break;
		}
	}
	
	/**
	 * Approves a client.
	 *
	 * @Route("/dashboard/client/approve/{id}", name="approve_client", requirements={"id": "\d+"})
	 * @Method({"GET"})
	 *
	 * @param int     $id
	 * @param Request $request A request instance
	 *
	 * @return RedirectResponse
	 */
	public function approveClientAction($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$allRouteParameters = $request->query->get('allRouteParameters');
		
		$client = $em->getRepository('NetFlexUserBundle:User')->findOneById($id);
		$client->setStatus(1);
		$client->setLastModifiedOn(new \DateTime());
		$client->setLastModifiedBy($this->getUser()->getId());
		
		$em->persist($client);
		$em->flush();
		
		/**
		 * Send mail.
		 */
		$email = $em->getRepository('NetFlexUserBundle:Email')->findClientPreferredOrFirstEmail($client->getId());
		if ($email) {
			$mailerService = $this->get('mailer_service');
			list($fromEmail, $fromName, $subject, $message) = $mailerService->getMailTemplateData('CLNT_ACC_APPRV');
			$message = $this->renderView('NetFlexMailerBundle::mail_layout.html.twig', [
				'mailBody' => $message,
			]);
			$message = str_replace(['[clientName]'], [$client->getFirstName() . ' ' . $client->getLastName()], $message);
			$message = html_entity_decode($message);
			$mailerService->setMessage($fromEmail, $email, $subject, $message, 1, $fromName, $client->getFirstName() . ' ' . $client->getLastName());
			$mailerService->sendMail();
		}
		
		$this->addFlash('success', 'Client approval was successful');
		
		return $this->redirectToRoute('client_list', $allRouteParameters);
	}
	
	/**
	 * Approves a client.
	 *
	 * @Route("/dashboard/client/bulk-approve", name="approve_multiple_clients")
	 * @Method({"GET"})
	 *
	 * @param int     $ids
	 * @param Request $request A request instance
	 *
	 * @return RedirectResponse
	 */
	public function approveMultipleClientAction($ids, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$allRouteParameters = $request->query->get('allRouteParameters');
		$ids = (false !== strpos($request->query->get('ids'), '-')) ? explode('-', $request->query->get('ids')) : [$request->query->get('ids')];
		
		foreach ($ids as $id) {
			$client = $em->getRepository('NetFlexUserBundle:User')->findOneById($id);
			$client->setStatus(1);
			$client->setLastModifiedOn(new \DateTime());
			$client->setLastModifiedBy($this->getUser()->getId());
			
			$em->persist($client);
			
			/**
			 * Send mail.
			 */
			$email = $em->getRepository('NetFlexUserBundle:Email')->findClientPreferredOrFirstEmail($client->getId());
			if ($email) {
				$mailerService = $this->get('mailer_service');
				list($fromEmail, $fromName, $subject, $message) = $mailerService->getMailTemplateData('CLNT_ACC_APPRV');
				$message = $this->renderView('NetFlexMailerBundle::mail_layout.html.twig', [
					'mailBody' => $message,
				]);
				$message = str_replace(['[clientName]'], [$client->getFirstName() . ' ' . $client->getLastName()], $message);
				$message = html_entity_decode($message);
				$mailerService->setMessage($fromEmail, $email, $subject, $message, 1, $fromName, $client->getFirstName() . ' ' . $client->getLastName());
				$mailerService->sendMail();
			}
		}
		
		$em->flush();
		
		$this->addFlash('success', 'Client approval was successful');
		
		return $this->redirectToRoute('client_list', $allRouteParameters);
	}
	
	/**
	 * Deletes a single client.
	 *
	 * @param int    $clientId
	 *
	 * @return array
	 */
	protected function deleteClient($clientId)
	{
		$em = $this->getDoctrine()->getManager();
		$clientRepo = $em->getRepository('NetFlexUserBundle:User');
		
		$thisClient = $clientRepo->findOneById($clientId);
		
		foreach ($thisClient->getEmails() as $email) {
			$em->remove($email);
		}
		foreach ($thisClient->getAddresses() as $address) {
			$em->remove($address);
		}
		foreach ($thisClient->getContacts() as $contact) {
			$em->remove($contact);
		}
		
		foreach ($thisClient->getRoles() as $role) {
			$thisClient->removeRole($em->getRepository('NetFlexUserBundle:Role')->findOneByName($role));
		}
		
		$orders = $em->getRepository('NetFlexOrderBundle:OrderTransaction')->findBy(['userId' => $thisClient->getId()]);
		foreach ($orders as $thisOrder) {
			$orderPrice = $thisOrder->getOrderPrice();
			$orderItem = $thisOrder->getOrderItem();
			$orderAddress = $thisOrder->getOrderAddress();
			
			$em->remove($orderPrice);
			$em->remove($orderItem);
			$em->remove($orderAddress);
			
			$em->remove($thisOrder);
		}
		
		$em->remove($thisClient);
		$em->flush();
		
		return ['status' => true];
	}
	
	/**
	 * Adjusts redirection page index.
	 *
	 * @param int $pageIndex
	 * @param int $selectedRecordCount
	 * @param int $totalRecordOnPage
	 *
	 * @return int
	 */
	protected function adjustPageIndex($pageIndex, $selectedRecordCount, $totalRecordOnPage)
	{
		if (0 === $selectedRecordCount) {
			return $pageIndex;
		}
		
		if ($selectedRecordCount === $totalRecordOnPage) {
			$pageIndex = (1 === $pageIndex) ? 1 : ($pageIndex - 1);
		}
		
		return $pageIndex;
	}
	
	/**
	 * @Route("/dashboard/client/exit-from-search-mode", name="exit_from_client_search_mode")
	 *
	 * @param  Request $request
	 *
	 * @return RedirectResponse
	 */
	public function exitFromSearchModeAction(Request $request)
	{
		$session = $request->getSession();
		$referrer = $request->query->get('ref');
		
		$session->remove('clientName');
		$session->remove('clientStatus');
		
		return $this->redirect($referrer);
	}
}
