<?php

namespace NetFlex\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NetFlex\UserBundle\Entity\Address;
use NetFlex\UserBundle\Entity\Email;
use NetFlex\UserBundle\Entity\Contact;
use NetFlex\UserBundle\Form\Front\ClientProfile\GeneralDetails;
use NetFlex\UserBundle\Form\Front\ClientProfile\ChangePassword;
use NetFlex\UserBundle\Form\Front\ClientProfile\BillingAndPickupAddresses;
use NetFlex\UserBundle\Form\Front\ClientProfile\ProfilePicture;

class ClientProfileController extends Controller
{
    /**
     * Renders client profile page.
     *
     * @Route("/client/profile", name="client_profile_page")
     * @Method({"GET", "POST"})
     *
     * @param  Request               $request A Request instance
     *
     * @return Response|JsonResponse          A Response instance
     */
    public function renderClientProfilePageAction(Request $request)
    {
	    if (! $this->get('security.authorization_checker')->isGranted('ROLE_CLIENT')) {
		    return $this->redirectToRoute('home_page');
	    }
	
	    $session = $request->getSession();
	    if (! $session->has('loggedInUsername')) {
		    $session->set('loggedInUsername', $this->getUser()->getUsername());
	    }
	
	    $id = $this->getUser()->getId();
	    $em = $this->getDoctrine()->getManager();
	    $client = $em->getRepository('NetFlexUserBundle:User')->findOneBy(['id' => $id, 'status' => 1]);
	    $profileImageUploadUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/' . $this->getParameter('generic_media_upload_directory_name') . '/';
	    if (0 === count($client->getEmails())) {
		    // Client has no associated email
		    $email = new Email();
		    $client->addEmail($email);
	    }
	    if (0 === count($client->getContacts())) {
		    // Client has no associated contacts
		    $contact = new Contact();
		    $client->addContact($contact);
	    }
	    
	    $gdForm = $this->createForm(GeneralDetails::class, $client);
	    $cpForm = $this->createForm(ChangePassword::class, $client);
	    $aForm = $this->createForm(BillingAndPickupAddresses::class, $client);
	
	    $gdForm->handleRequest($request);
	    if ($gdForm->isSubmitted()) {
		    $errors = $this->get('validator')->validate($gdForm);
		    if (0 < count($errors)) {
			    $errorList = [];
			    foreach ($errors as $error) {
				    $errorList[substr($error->getPropertyPath(), (strrpos($error->getPropertyPath(), '.') + 1))] = $error->getMessage();
			    }
			    return $this->json(['status' => false, 'errorList' => $errorList]);
		    }
		    
		    foreach ($client->getEmails() as $email) {
			    if (! $email->getId()) {
				    $email->setIsPrimary(1);
				    $email->setStatus(1);
			    }
			    $email->setUserId($client);
			    $em->persist($email);
		    }
		    foreach ($client->getContacts() as $contact) {
			    if (! $contact->getId()) {
				    $contact->setIsPrimary(1);
				    $contact->setStatus(1);
			    }
			    $contact->setUserId($client);
			    $em->persist($contact);
		    }
		    
		    $em->flush();
		    
		    return $this->json(['status' => true]);
	    }
	    
	    $cpForm->handleRequest($request);
	    if ($cpForm->isSubmitted()) {
		    $oldPassword = $cpForm->get('oldPassword')->getData();
		    $repeatPassword = $cpForm->get('repeatPassword')->getData();
		    $errorList = [];
		    
		    if (! $oldPassword) {
			    $errorList['oldPassword'] = 'This field is required';
		    }
		    if (! $repeatPassword) {
			    $errorList['repeatPassword'] = 'This field is required';
		    }
		    if (! $client->getPassword()) {
			    $errorList['password'] = 'This field is required';
		    }
		    if ($client->getPassword() !== $repeatPassword) {
			    $errorList['repeatPassword'] = 'Doesn\'t match with new password';
		    }
		    $errors = $this->get('validator')->validate($cpForm);
		    if (0 < count($errors)) {
			    foreach ($errors as $error) {
				    $errorList[substr($error->getPropertyPath(), (strrpos($error->getPropertyPath(), '.') + 1))] = $error->getMessage();
			    }
		    }
		    if ($errorList) {
			    return $this->json(['status' => false, 'errorList' => $errorList]);
		    }
		    
		    $client->setPassword($this->get('security.password_encoder')->encodePassword($client, $client->getPassword()));
		    $em->persist($client);
		    $em->flush();
		    
		    return $this->json(['status' => true]);
	    }
	
	    if ($client->getProfileImage()) {
		    $client->setProfileImage(new File($this->getParameter('client_profile_image_upload_directory_name') . '/' . $client->getProfileImage()));
	    }
	    $ppForm = $this->createForm(ProfilePicture::class, $client);
	    $ppForm->handleRequest($request);
	    if ($ppForm->isSubmitted()) {
		    if ($ppForm->isValid()) {
			    $file = $client->getProfileImage();
			    $tmpImagePath = $file->getPathName();
			    $fileExtension = $file->guessExtension();
			    $fileName = 'client_' . $client->getId() . '_' . md5(uniqid()) . '.' . $fileExtension;
			
			    /**
			     * Scale image.
			     */
			    $sourceImage = imagecreatefromjpeg($tmpImagePath);
			    $width = imagesx($sourceImage);
			    $height = imagesy($sourceImage);
			    $desiredWidth = $desiredHeight = $this->getParameter('client_profile_image_max_width_and_height');
			    $scale = min(($desiredWidth / $width), ($desiredHeight / $height));
			    $newWidth = ($width * $scale);
			    $newHeight = ($height * $scale);
			    $virtualImage = imagecreatetruecolor($newWidth, $newHeight);
			    imagecopyresampled($virtualImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
			    imagejpeg($virtualImage, $tmpImagePath);
			
			    /**
			     * Move image.
			     */
			    $file->move(
				    $this->getParameter('client_profile_image_upload_directory_path'),
				    $fileName
			    );
			
			    /**
			     * Persist.
			     */
			    $client->setProfileImage($fileName);
			    $em->persist($client);
			    $em->flush();
			    
			    return $this->redirectToRoute('client_profile_page');
		    } else {
			    $clientProfileImage = $em->getRepository('NetFlexUserBundle:User')->findUserProfileImage($client->getId());
			    if ($clientProfileImage) {
				    $client->setProfileImage(new File($this->getParameter('client_profile_image_upload_directory_name') . '/' . $clientProfileImage));
			    } else {
				    $client->setProfileImage(null);
			    }
		    }
	    }
	    
	    return $this->render('NetFlexFrontBundle:Profile:client.html.twig', [
        	'pageTitle' => 'My Account',
		    'client' => $client,
		    'gdForm' => $gdForm->createView(),
		    'cpForm' => $cpForm->createView(),
		    'aForm' => $aForm->createView(),
		    'ppForm' => $ppForm->createView(),
		    'profileImageUploadUrl' => $profileImageUploadUrl,
        ]);
    }
	
	/**
	 * Updates client address.
	 *
	 * @Route("/client/profile/update-address", name="update_client_address")
	 * @Method({"POST"})
	 *
	 * @param  Request      $request A Request instance
	 *
	 * @return JsonResponse          A Response instance
	 */
    public function updateClientAddressAction(Request $request)
    {
	    if (! $this->get('security.authorization_checker')->isGranted('ROLE_CLIENT')) {
		    return $this->json(['status' => true]);
	    }
	    
	    $em = $this->getDoctrine()->getManager();
	    
	    // Find client entity.
	    $client = $em->getRepository('NetFlexUserBundle:User')->findOneById($request->request->get('userId'));
	    
	    // Find address entity.
	    $address = $em->getRepository('NetFlexUserBundle:Address')->findOneById($request->request->get('addressId'));
	    
	    // Remove this associated address entity from client entity.
	    $client->removeAddress($address);
	
	    // Manipulate submitted data.
	    $country = $state = $city = null;
	    if (is_numeric($countryId = $request->request->get('countryId'))) {
		    $country = $em->getRepository('NetFlexLocationBundle:Country')->findOneById($countryId);
	    } else {
		    $country = $em->getRepository('NetFlexLocationBundle:Country')->findOneByName($countryId);
	    }
	    if ($country) {
		    if (is_numeric($stateId = $request->request->get('stateId'))) {
			    $state = $em->getRepository('NetFlexLocationBundle:State')->findOneBy(['countryId' => $country, 'id' => $stateId]);
		    } else {
			    $state = $em->getRepository('NetFlexLocationBundle:State')->findOneBy(['countryId' => $country, 'name' => $stateId]);
		    }
	    }
	    if ($state) {
		    if (is_numeric($cityId = $request->request->get('cityId'))) {
			    $city = $em->getRepository('NetFlexLocationBundle:City')->findOneBy(['countryId' => $country, 'stateId' => $state, 'id' => $cityId]);
		    } else {
			    $city = $em->getRepository('NetFlexLocationBundle:City')->findOneBy(['countryId' => $country, 'stateId' => $state, 'name' => $cityId]);
		    }
	    }
	    
	    // Set updated data to this address entity.
	    $address->setAddressLine1($request->request->get('addressLine1'));
	    $address->setAddressLine2($request->request->get('addressLine2'));
	    $address->setCountryId($country);
	    $address->setStateId($state);
	    $address->setCityId($city);
	    $address->setZipCode($request->request->get('zipCode'));
	    $address->setIsPrimary($request->request->get('isPrimary'));
	    
	    // Validate.
	    $errorList = [];
	    $errors = $this->get('validator')->validate($address);
	    if (0 < count($errors)) {
		    foreach ($errors as $error) {
			    $propertyPath = substr($error->getPropertyPath(), (strrpos($error->getPropertyPath(), '.')));
			    if ('countryId' === $propertyPath) {
				    $errorList[$propertyPath] = 'Country doesn\'t exist';
			    } elseif ('stateId' === $propertyPath) {
				    $errorList[$propertyPath] = 'State doesn\'t exist';
			    } elseif ('cityId' === $propertyPath) {
				    $errorList[$propertyPath] = 'City doesn\'t exist';
			    } else {
				    $errorList[$propertyPath] = $error->getMessage();
			    }
		    }
	    }
	    if ($request->request->get('isPrimary')) {
		    foreach ($client->getAddresses() as $address) {
			    if (($request->request->get('addressTypeId') == $address->getAddressTypeId()->getId()) && ($address->getIsPrimary())) {
				    $errorList['isPrimary'] = 'One default address already exists';
				    
				    break;
			    }
		    }
	    }
	    if ($errorList) {
		    return $this->json(['status' => false, 'errorList' => $errorList]);
	    }
	    
	    // Re-associate this address entity to client entity
	    $address->setUserId($client);
	    
	    // Persist and flush.
	    $em->persist($address);
	    $em->flush();
	    
	    return $this->json(['status' => true]);
    }
	
	/**
	 * Deletes client address.
	 *
	 * @Route("/client/profile/delete-address", name="delete_client_address")
	 * @Method({"POST"})
	 *
	 * @param  Request      $request A Request instance
	 *
	 * @return JsonResponse          A Response instance
	 */
	public function deleteClientAddressAction(Request $request)
	{
		if (! $this->get('security.authorization_checker')->isGranted('ROLE_CLIENT')) {
			return $this->json(['status' => true]);
		}
		
		$em = $this->getDoctrine()->getManager();
		
		// Find client entity.
		$client = $em->getRepository('NetFlexUserBundle:User')->findOneById($request->request->get('userId'));
		
		// Find address entity.
		$address = $em->getRepository('NetFlexUserBundle:Address')->findOneById($request->request->get('addressId'));
		
		$em->remove($address);
		$em->flush();
		
		return $this->json(['status' => true]);
	}
	
	/**
	 * Adds new client address.
	 *
	 * @Route("/client/profile/add-address", name="add_client_address")
	 * @Method({"POST"})
	 *
	 * @param  Request      $request A Request instance
	 *
	 * @return JsonResponse          A Response instance
	 */
	public function addClientAddressAction(Request $request)
	{
		if (! $this->get('security.authorization_checker')->isGranted('ROLE_CLIENT')) {
			return $this->json(['status' => true]);
		}
		
		$em = $this->getDoctrine()->getManager();
		
		// Find client entity.
		$client = $em->getRepository('NetFlexUserBundle:User')->findOneById($request->request->get('userId'));
		
		// Create empty address entity.
		$address = new Address();
		
		// Manipulate submitted data.
		$country = $state = $city = null;
		if (is_numeric($countryId = $request->request->get('countryId'))) {
			$country = $em->getRepository('NetFlexLocationBundle:Country')->findOneById($countryId);
		} else {
			$country = $em->getRepository('NetFlexLocationBundle:Country')->findOneByName($countryId);
		}
		if ($country) {
			if (is_numeric($stateId = $request->request->get('stateId'))) {
				$state = $em->getRepository('NetFlexLocationBundle:State')->findOneBy(['countryId' => $country, 'id' => $stateId]);
			} else {
				$state = $em->getRepository('NetFlexLocationBundle:State')->findOneBy(['countryId' => $country, 'name' => $stateId]);
			}
		}
		if ($state) {
			if (is_numeric($cityId = $request->request->get('cityId'))) {
				$city = $em->getRepository('NetFlexLocationBundle:City')->findOneBy(['countryId' => $country, 'stateId' => $state, 'id' => $cityId]);
			} else {
				$city = $em->getRepository('NetFlexLocationBundle:City')->findOneBy(['countryId' => $country, 'stateId' => $state, 'name' => $cityId]);
			}
		}
		
		// Set data to this address entity.
		$address->setAddressTypeId($em->getRepository('NetFlexUserBundle:AddressType')->findOneById((int) $request->request->get('addressTypeId')));
		$address->setAddressLine1($request->request->get('addressLine1'));
		$address->setAddressLine2($request->request->get('addressLine2'));
		$address->setCountryId($country);
		$address->setStateId($state);
		$address->setCityId($city);
		$address->setZipCode($request->request->get('zipCode'));
		$address->setIsPrimary($request->request->get('isPrimary'));
		$address->setStatus(1);
		
		// Validate.
		$errorList = [];
		$errors = $this->get('validator')->validate($address);
		if (0 < count($errors)) {
			foreach ($errors as $error) {
				$propertyPath = substr($error->getPropertyPath(), (strrpos($error->getPropertyPath(), '.')));
				if ('countryId' === $propertyPath) {
					$errorList[$propertyPath] = 'Country doesn\'t exist';
				} elseif ('stateId' === $propertyPath) {
					$errorList[$propertyPath] = 'State doesn\'t exist';
				} elseif ('cityId' === $propertyPath) {
					$errorList[$propertyPath] = 'City doesn\'t exist';
				} else {
					$errorList[$propertyPath] = $error->getMessage();
				}
			}
		}
		if ($request->request->get('isPrimary')) {
			foreach ($client->getAddresses() as $thisAddress) {
				if (($request->request->get('addressTypeId') == $thisAddress->getAddressTypeId()->getId()) && ($thisAddress->getIsPrimary())) {
					$errorList['isPrimary'] = 'One default address already exists';
					
					break;
				}
			}
		}
		if ($errorList) {
			return $this->json(['status' => false, 'errorList' => $errorList]);
		}
		
		// Associate the client entity with this address entity
		$address->setUserId($client);
		
		// Persist and flush.
		$em->persist($address);
		$em->flush();
		
		return $this->json(['status' => true]);
	}
	
	/**
	 * Gets a list of countries.
	 *
	 * @Route("/client/profile/get-country-list", name="get_country_list")
	 * @Method({"GET"})
	 *
	 * @param  Request      $request A Request instance
	 *
	 * @return JsonResponse          A Response instance
	 */
	public function getCountryList(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$countryList = [];
		$countryHint = $request->query->get('countryHint');
		
		// Find matched countries if any and populate country list.
		$countries = $em->getRepository('NetFlexLocationBundle:Country')->findCountriesByName($countryHint);
		if ($countries) {
			foreach ($countries as $country) {
				$countryList[] = $country->getName();
			}
		}
		
		return $this->json(['countryList' => $countryList]);
	}
	
	/**
	 * Gets a list of states under a country.
	 *
	 * @Route("/client/profile/get-state-list", name="get_state_list")
	 * @Method({"GET"})
	 *
	 * @param  Request      $request A Request instance
	 *
	 * @return JsonResponse          A Response instance
	 */
	public function getStateList(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$stateList = [];
		$countryName = $request->query->get('countryName');
		$stateHint = $request->query->get('stateHint');
		
		// Find matched states if any and populate state list.
		$states = $em->getRepository('NetFlexLocationBundle:State')->findStatesInACountryByName($countryName, $stateHint);
		if ($states) {
			foreach ($states as $state) {
				$stateList[] = $state->getName();
			}
		}
		
		return $this->json(['stateList' => $stateList]);
	}
	
	/**
	 * Gets a list of cities under a country and a state.
	 *
	 * @Route("/client/profile/get-city-list", name="get_city_list")
	 * @Method({"GET"})
	 *
	 * @param  Request      $request A Request instance
	 *
	 * @return JsonResponse          A Response instance
	 */
	public function getCityList(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$cityList = [];
		$countryName = $request->query->get('countryName');
		$stateName = $request->query->get('stateName');
		$cityHint = $request->query->get('cityHint');
		
		// Find matched city if any and populate city list.
		$cities = $em->getRepository('NetFlexLocationBundle:City')->findCitiesInACountryAndStateByName($countryName, $stateName, $cityHint);
		if ($cities) {
			foreach ($cities as $city) {
				$cityList[] = $city->getName();
			}
		}
		
		return $this->json(['cityList' => $cityList]);
	}
}
