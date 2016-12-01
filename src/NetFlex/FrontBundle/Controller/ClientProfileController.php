<?php

namespace NetFlex\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NetFlex\UserBundle\Form\Front\ClientProfile\GeneralDetails;

class ClientProfileController extends Controller
{
    /**
     * Renders client profile page.
     *
     * @Route("/client/profile", name="client_profile_page")
     * @Method({"GET", "POST"})
     *
     * @param  Request $request A Request instance
     *
     * @return Response          A Response instance
     */
    public function renderClientProfilePageAction(Request $request)
    {
        $id = $request->query->get('id');
	    $em = $this->getDoctrine()->getManager();
	    $client = $em->getRepository('NetFlexUserBundle:User')->findClientProfileData($id);
	    $gdForm = $this->createForm(GeneralDetails::class, $client);
	    
	    $gdForm->handleRequest($request);
	    if ($gdForm->isSubmitted()) {
		    var_dump($gdForm->getData());exit;
	    }
	    
	    return $this->render('NetFlexFrontBundle:Profile:client.html.twig', [
        	'pageTitle' => 'My Account',
		    'client' => $client,
		    'gdForm' => $gdForm->createView(),
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
