<?php

namespace NetFlex\LocationBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NetFlex\LocationBundle\Entity\City;
use NetFlex\LocationBundle\Form\City\CityEditType;
use NetFlex\LocationBundle\Form\City\CityNewType;

class CityController extends Controller
{
    /**
     * Renders city list in dashboard.
     *
     * @Route("/dashboard/city/list/{page}/{sortColumn}/{sortOrder}", name="dashboard_city_list", defaults={"page": 1, "sortColumn": "id", "sortOrder": "desc"}, requirements={"page": "\d+", "sortColumn": "id|country|state|name|status", "sortOrder": "asc|desc"})
     * @Method({"GET", "POST"})
     *
     * @param  int                       $page
     * @param  string                    $sortColumn
     * @param  string                    $sortOrder
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardCityListAction($page, $sortColumn, $sortOrder, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $paginationService = $this->get('pagination_service');
        
        $routeParameters = compact('page', 'sortColumn', 'sortOrder');
        
        $cityName = ($session->has('cityName')) ? $session->get('cityName') : null;
        $sortColumnFormatted = $this->getSortColumnFormatted($sortColumn);
        $sortOrderFormatted = strtoupper($sortOrder);
        
        $searchForm = $this->createFormBuilder()
        ->setAction($this->generateUrl('dashboard_city_list', [], UrlGeneratorInterface::ABSOLUTE_URL))
        ->setMethod('POST')
        ->add('cityName', TextType::class, [
            'data' => $cityName,
        ])
        ->getForm();
        
        $searchForm->handleRequest($request);
        
        if ($searchForm->isSubmitted()) {
            $formData = $searchForm->getData();
            
            $session->set('cityName', $formData['cityName']);
            
            return $this->redirectToRoute('dashboard_city_list');
        }
        
        $cityCount = $em->getRepository('NetFlexLocationBundle:City')->findCityCount($cityName, $sortColumnFormatted, $sortOrderFormatted);
    
        $limit = 10;
        $neighbor = 3;
        $offset = $paginationService->getRecordOffset($page, $limit);
        
        $cities = $em->getRepository('NetFlexLocationBundle:City')->findCity($offset, $limit, $cityName, $sortColumnFormatted, $sortOrderFormatted);
    
        $totalPageCount = $paginationService->getTotalPageCount($limit, $cityCount);
        $pageLinks = $paginationService->getPageLinks($page, $limit, $neighbor, $cityCount, $totalPageCount, 'dashboard_city_list', $routeParameters, []);
    
        $breadCrumbs = [
            [
                'title' => 'Dashboard Home',
                'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'title' => 'City List',
                'link' => $this->generateUrl('dashboard_city_list', $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL),
            ],
        ];
    
        return $this->render('NetFlexLocationBundle:City:list.html.twig', [
            'pageTitle' => 'City List',
            'breadCrumbs' => $breadCrumbs,
            'pageHeader' => '<h1>City<small> list </small></h1>',
            'listHeader' => 'City List',
            'searchForm' => $searchForm->createView(),
            'cityCount' => $cityCount,
            'totalPageCount' => $totalPageCount,
            'cities' => $cities,
            'pageLinks' => $pageLinks,
            'referer' => urlencode($this->generateUrl('dashboard_city_list', $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL)),
            'routeParameters' => $routeParameters,
            'allRouteParameters' => $routeParameters,
        ]);
    }
    
    /**
     * Exits from city search in dashboard.
     *
     * @Route("/dashboard/city/exit-search", name="dashboard_city_exit_search")
     * @Method({"GET"})
     *
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardCityExitSearchAction(Request $request)
    {
        $session = $request->getSession();
        
        $session->remove('cityName');
        
        return $this->redirectToRoute('dashboard_city_list');
    }
    
    /**
     * Renders city add page and also saves a city record.
     *
     * @Route("/dashboard/city/new", name="dashboard_city_new")
     * @Method({"GET", "POST"})
     *
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardCityNewAction(Request $request)
    {
        $city = new City();
        
        $form = $this->createForm(CityNewType::class, $city);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $currentDateTime = new \DateTime();
            $currentUser = $this->getUser()->getId();
            
            $city->setStatus(1);
            $city->setCreatedOn($currentDateTime);
            $city->setCreatedBy($currentUser);
            $city->setLastModifiedOn($currentDateTime);
            $city->setLastModifiedBy($currentUser);
            
            $em->persist($city);
            $em->flush();
    
            $this->addFlash('success', 'New city has been added successfully');
    
            return $this->redirectToRoute('dashboard_city_edit', ['cityId' => $city->getId()]);
        }
    
        $breadCrumbs = [
            [
                'title' => 'Dashboard Home',
                'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'title' => 'City List',
                'link' => $this->generateUrl('dashboard_city_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'title' => 'Add New City',
                'link' => $this->generateUrl('dashboard_city_new', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
        ];
    
        return $this->render('NetFlexLocationBundle:City:new.html.twig', [
            'pageTitle' => 'Add New City',
            'breadCrumbs' => $breadCrumbs,
            'pageHeader' => '<h1>City<small> new </small></h1>',
            'listHeader' => 'Add New City',
            'form' => $form->createView(),
            'referer' => ($request->query->has('referer')) ? urldecode($request->query->get('referer')) : $this->generateUrl('dashboard_city_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }
    
    /**
     * Renders city edit page and also updates city record.
     *
     * @Route("/dashboard/city/edit/{cityId}", name="dashboard_city_edit", requirements={"cityId": "\d+"})
     * @Method({"GET", "POST"})
     *
     * @param  int                       $cityId
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardCityEditAction($cityId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $city = $em->getRepository('NetFlexLocationBundle:City')->findOneBy(['id' => $cityId]);
        
        if (! $city) {
            throw $this->createNotFoundException('City was not found');
        } else {
            $form = $this->createForm(CityEditType::class, $city);
            
            $form->handleRequest($request);
            
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $city->setLastModifiedOn(new \DateTime());
                    $city->setLastModifiedBy($this->getUser()->getId());
    
                    $em->persist($city);
                    $em->flush();
    
                    $this->addFlash('success', 'City details has been updated successfully');
    
                    return $this->redirectToRoute('dashboard_city_edit', ['cityId' => $city->getId()]);
                }
            }
    
            $breadCrumbs = [
                [
                    'title' => 'Dashboard Home',
                    'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
                ],
                [
                    'title' => 'City List',
                    'link' => $this->generateUrl('dashboard_city_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
                ],
                [
                    'title' => 'Edit City',
                    'link' => $this->generateUrl('dashboard_city_edit', ['cityId' => $cityId], UrlGeneratorInterface::ABSOLUTE_URL),
                ],
            ];
    
            return $this->render('NetFlexLocationBundle:City:edit.html.twig', [
                'pageTitle' => 'Edit City',
                'breadCrumbs' => $breadCrumbs,
                'pageHeader' => '<h1>City<small> edit </small></h1>',
                'listHeader' => 'Edit City',
                'city' => $city,
                'form' => $form->createView(),
                'referer' => ($request->query->has('referer')) ? urldecode($request->query->get('referer')) : $this->generateUrl('dashboard_city_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
        }
    }
    
    /**
     * Change city status.
     *
     * @Route("/dashboard/city/change-status/{changeStatusTo}/{cityId}", name="dashboard_city_change_status", requirements={"changeStatusTo": "activate|deactivate", "cityId": "\d+"})
     * @Method({"GET"})
     *
     * @param  string                    $changeStatusTo
     * @param  int                       $cityId
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardCityChangeStatusAction($changeStatusTo, $cityId, Request $request)
    {
        $changeStatusTo = ('deactivate' == $changeStatusTo) ? 0 : 1;
        $referer = $request->query->has('referer') ? urldecode($request->query->get('referer')) : $this->generateUrl('dashboard_city_list', [], UrlGeneratorInterface::ABSOLUTE_URL);
        
        if (! $this->dashboardCityChangeStatus($cityId, $changeStatusTo)) {
            $this->addFlash('error', 'Status for selected city could not be changed successfully');
        } else {
            $this->addFlash('success', 'Status for selected city was changed successfully');
        }
        
        return $this->redirect($referer);
    }
    
    /**
     * Change multiple city status at a go.
     *
     * @Route("/dashboard/city/bulk-change-status", name="dashboard_city_bulk_status_change")
     * @Method({"POST"})
     *
     * @param  Request      $request
     *
     * @return JsonResponse
     */
    public function dashboardCityBulkChangeStatusAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $cityIds = $request->request->get('cityIds');
            $cityIds = (false === strpos($cityIds, '-')) ? [$cityIds] : explode('-', $cityIds);
            $changeStatusTo = $request->request->get('changeStatusTo');
            $unaffectedCityIds = [];
            
            foreach ($cityIds as $thisCityId) {
                if (! $this->dashboardCityChangeStatus($thisCityId, $changeStatusTo)) {
                    $unaffectedCityIds[] = $thisCityId;
                }
            }
            
            if ($unaffectedCityIds) {
                if (count($unaffectedCityIds) == count($cityIds)) {
                    $this->addFlash('error', 'Status for selected cities could not be changed successfully');
                } else {
                    $this->addFlash('warning', 'Status for cities with ID: ' . implode(', ', $unaffectedCityIds) . ' could not be changed');
                }
            } else {
                $this->addFlash('success', 'Status for selected cities were changed successfully');
            }
            
            return $this->json(['status' => 'complete']);
        }
    }
    
    /**
     * Get state list.
     *
     * @Route("/dashboard/city/state-list", name="dashboard_city_state_list")
     * @Method({"POST"})
     *
     * @param  Request      $request
     *
     * @return JsonResponse
     */
    public function dashboardCityStateListAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $stateList = [];
    
            $countryId = $request->request->get('countryId');
            
            if (! $countryId) {
                return $this->json(['stateList' => $stateList]);
            } else {
                $em = $this->getDoctrine()->getManager();
    
                $states = $em->getRepository('NetFlexLocationBundle:State')->findBy(['countryId' => $countryId, 'status' => 1]);
                
                if (! $states) {
                    return $this->json(['stateList' => $stateList]);
                } else {
                    foreach ($states as $thisState) {
                        if (in_array($thisState->getId(), [42, 43, 44, 45, 46, 47])) {
                            continue;
                        } else {
                            $stateList[$thisState->getId()] = $thisState->getName();
                        }
                    }
    
                    return $this->json(['stateList' => $stateList]);
                }
            }
        }
    }
    
    /**
     * Gets sort column formatted.
     *
     * @param  string $sortColumn
     *
     * @return string
     */
    private function getSortColumnFormatted($sortColumn)
    {
        $sortColumnFormatted = 'id';
        
        switch ($sortColumn) {
            case 'country':
                $sortColumnFormatted = 'countryId';
                
                break;
                
            case 'state':
                $sortColumnFormatted = 'stateId';
                
                break;
                
            case 'name':
                $sortColumnFormatted = 'name';
                
                break;
    
            case 'status':
                $sortColumnFormatted = 'status';
        
                break;
                
            case 'id':
            default:
                break;
        }
        
        return $sortColumnFormatted;
    }
    
    /**
     * Changes status of a selected city.
     *
     * @param int  $cityId
     * @param int  $changeStatusTo
     *
     * @return bool
     */
    private function dashboardCityChangeStatus($cityId, $changeStatusTo)
    {
        $em = $this->getDoctrine()->getManager();
        
        $city = $em->getRepository('NetFlexLocationBundle:City')->findOneById($cityId);
        
        if (! $city) {
            return false;
        } else {
            $city->setStatus($changeStatusTo);
            $city->setLastModifiedOn(new \DateTime());
            $city->setLastModifiedBy($this->getUser()->getId());
            
            $em->persist($city);
            $em->flush();
            
            return true;
        }
    }
}
