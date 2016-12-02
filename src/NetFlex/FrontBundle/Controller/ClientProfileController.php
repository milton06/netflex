<?php

namespace NetFlex\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NetFlex\UserBundle\Form\Front\ClientProfile\GeneralDetails;
use NetFlex\UserBundle\Form\Front\ClientProfile\ChangePassword;
use NetFlex\UserBundle\Form\Front\ClientProfile\BillingOrPickupAddress;

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
	    $id = 9; // Test client ID; would be replaced by logged in client ID.
	    $em = $this->getDoctrine()->getManager();
	    $client = $em->getRepository('NetFlexUserBundle:User')->findClientProfileData($id);
	    $clientAddresses = $em->getRepository('NetFlexUserBundle:Address')->findClientPreferredPickupAndBillingAddresses($id);
	    $clientBillingAddressCount = $clientAddresses[0]['billingAddressCount'];
	    unset($clientAddresses[0]);
	    list($clientBillingAddresses, $clientPickupAddresses) = array_chunk($clientAddresses, $clientBillingAddressCount);
	    foreach ($clientBillingAddresses as $key => $clientBillingAddress) {
		    $clientBillingAddresses[$key] = $clientBillingAddress[0];
	    }
	    $gdForm = $this->createForm(GeneralDetails::class, $client);
	    $cpForm = $this->createForm(ChangePassword::class, $client);
	    $baForms = $baFormViews = [];
	    foreach ($clientBillingAddresses as $key => $clientBillingAddress) {
		    $baForms[$key] = $this->createForm(BillingOrPickupAddress::class, $clientBillingAddress);
		    $baFormViews[$key] = $baForms[$key]->createView();
	    }
	    
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
		    
		    $em->persist($client);
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
	    
	    foreach ($baForms as $key => $baForm) {
		    $baForm->handleRequest($request);
		    if ($baForm->isSubmitted()) {
			    $em->persist($clientBillingAddresses[$key]);
			    $em->flush();
		    }
	    }
	    
	    return $this->render('NetFlexFrontBundle:Profile:client.html.twig', [
        	'pageTitle' => 'My Account',
		    'client' => $client,
		    'clientBillingAddresses' => $clientBillingAddresses,
		    'clientPickupAddresses' => $clientPickupAddresses,
		    'gdForm' => $gdForm->createView(),
		    'cpForm' => $cpForm->createView(),
		    'baForms' => $baFormViews,
        ]);
    }
	
	/**
	 * Update client profile general details.
	 *
	 * @Route("/client/profile", name="update_client_profile_general_details")
	 * @Method({"POST"})
	 *
	 * @param  Request      $request A Request instance
	 *
	 * @return JsonResponse          A Response instance
	 */
    public function updateClientProfileGeneralDetailsAction(Request $request)
    {
	    
    }
}
