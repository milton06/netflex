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
use NetFlex\LocationBundle\Entity\Country;
use NetFlex\LocationBundle\Form\Country\CountryEditType;
use NetFlex\LocationBundle\Form\Country\CountryNewType;

class CountryController extends Controller
{
    /**
     * Renders country list in dashboard.
     *
     * @Route("/dashboard/country/list/{page}/{sortColumn}/{sortOrder}", name="dashboard_country_list",
     *     defaults={"page":
     *      1, "sortColumn": "id", "sortOrder": "desc"}, requirements={"page": "\d+", "sortColumn":
     *     "id|code|name|status", "sortOrder": "asc|desc"})
     * @Method({"GET", "POST"})
     *
     * @param  int                       $page
     * @param  string                    $sortColumn
     * @param  string                    $sortOrder
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardCountryListAction($page, $sortColumn, $sortOrder, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $paginationService = $this->get('pagination_service');
        
        $routeParameters = compact('page', 'sortColumn', 'sortOrder');
        
        $countryName = ($session->has('countryName')) ? $session->get('countryName') : null;
        $sortOrderFormatted = strtoupper($sortOrder);
        
        $searchForm = $this->createFormBuilder()
        ->setAction($this->generateUrl('dashboard_country_list', [], UrlGeneratorInterface::ABSOLUTE_URL))
        ->setMethod('POST')
        ->add('countryName', TextType::class, [
            'data' => $countryName,
        ])
        ->getForm();
        
        $searchForm->handleRequest($request);
        
        if ($searchForm->isSubmitted()) {
            $formData = $searchForm->getData();
            
            $session->set('countryName', $formData['countryName']);
            
            return $this->redirectToRoute('dashboard_country_list');
        }
        
        $countryCount = $em->getRepository('NetFlexLocationBundle:Country')->findCountryCount($countryName,
            $sortColumn, $sortOrderFormatted);
    
        $limit = 10;
        $neighbor = 3;
        $offset = $paginationService->getRecordOffset($page, $limit);
        
        $countries = $em->getRepository('NetFlexLocationBundle:Country')->findCountry($offset, $limit, $countryName,
            $sortColumn, $sortOrderFormatted);
    
        $totalPageCount = $paginationService->getTotalPageCount($limit, $countryCount);
        $pageLinks = $paginationService->getPageLinks($page, $limit, $neighbor, $countryCount, $totalPageCount, 'dashboard_country_list', $routeParameters, []);
    
        $breadCrumbs = [
            [
                'title' => 'Dashboard Home',
                'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'title' => 'Countrty List',
                'link' => $this->generateUrl('dashboard_country_list', $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL),
            ],
        ];
    
        return $this->render('NetFlexLocationBundle:Country:list.html.twig', [
            'pageTitle' => 'Country List',
            'breadCrumbs' => $breadCrumbs,
            'pageHeader' => '<h1>Country<small> list </small></h1>',
            'listHeader' => 'Country List',
            'searchForm' => $searchForm->createView(),
            'countryCount' => $countryCount,
            'totalPageCount' => $totalPageCount,
            'countries' => $countries,
            'pageLinks' => $pageLinks,
            'referer' => urlencode($this->generateUrl('dashboard_country_list', $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL)),
            'routeParameters' => $routeParameters,
            'allRouteParameters' => $routeParameters,
        ]);
    }
    
    /**
     * Exits from country search in dashboard.
     *
     * @Route("/dashboard/country/exit-search", name="dashboard_country_exit_search")
     * @Method({"GET"})
     *
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardCountryExitSearchAction(Request $request)
    {
        $session = $request->getSession();
        
        $session->remove('countryName');
        
        return $this->redirectToRoute('dashboard_country_list');
    }
    
    /**
     * Renders country add page and also saves a country record.
     *
     * @Route("/dashboard/country/new", name="dashboard_country_new")
     * @Method({"GET", "POST"})
     *
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardCountryNewAction(Request $request)
    {
        $country = new Country();
        
        $form = $this->createForm(CountryNewType::class, $country);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $currentDateTime = new \DateTime();
            $currentUser = $this->getUser()->getId();
    
            $country->setStatus(1);
            $country->setCreatedOn($currentDateTime);
            $country->setCreatedBy($currentUser);
            $country->setLastModifiedOn($currentDateTime);
            $country->setLastModifiedBy($currentUser);
            
            $em->persist($country);
            $em->flush();
    
            $this->addFlash('success', 'New country has been added successfully');
    
            return $this->redirectToRoute('dashboard_country_edit', ['countryId' => $country->getId()]);
        }
    
        $breadCrumbs = [
            [
                'title' => 'Dashboard Home',
                'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'title' => 'Country List',
                'link' => $this->generateUrl('dashboard_country_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'title' => 'Add New Country',
                'link' => $this->generateUrl('dashboard_country_new', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
        ];
    
        return $this->render('NetFlexLocationBundle:Country:new.html.twig', [
            'pageTitle' => 'Add New Country',
            'breadCrumbs' => $breadCrumbs,
            'pageHeader' => '<h1>Country<small> new </small></h1>',
            'listHeader' => 'Add New Country',
            'form' => $form->createView(),
            'referer' => ($request->query->has('referer')) ? urldecode($request->query->get('referer')) : $this->generateUrl('dashboard_country_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }
    
    /**
     * Renders country edit page and also updates country record.
     *
     * @Route("/dashboard/country/edit/{countryId}", name="dashboard_country_edit", requirements={"countryId": "\d+"})
     * @Method({"GET", "POST"})
     *
     * @param  int                       $countryId
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardCountryEditAction($countryId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $country = $em->getRepository('NetFlexLocationBundle:Country')->findOneBy(['id' => $countryId]);
        
        if (! $country) {
            throw $this->createNotFoundException('Country was not found');
        } else {
            $form = $this->createForm(CountryEditType::class, $country);
            
            $form->handleRequest($request);
            
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $country->setLastModifiedOn(new \DateTime());
                    $country->setLastModifiedBy($this->getUser()->getId());
    
                    $em->persist($country);
                    $em->flush();
    
                    $this->addFlash('success', 'Country details has been updated successfully');
    
                    return $this->redirectToRoute('dashboard_country_edit', ['countryId' => $countryId]);
                }
            }
    
            $breadCrumbs = [
                [
                    'title' => 'Dashboard Home',
                    'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
                ],
                [
                    'title' => 'Country List',
                    'link' => $this->generateUrl('dashboard_country_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
                ],
                [
                    'title' => 'Edit Country',
                    'link' => $this->generateUrl('dashboard_country_edit', ['countryId' => $countryId],
                        UrlGeneratorInterface::ABSOLUTE_URL),
                ],
            ];
    
            return $this->render('NetFlexLocationBundle:Country:edit.html.twig', [
                'pageTitle' => 'Edit Country',
                'breadCrumbs' => $breadCrumbs,
                'pageHeader' => '<h1>Country<small> edit </small></h1>',
                'listHeader' => 'Edit Country',
                'country' => $country,
                'form' => $form->createView(),
                'referer' => ($request->query->has('referer')) ? urldecode($request->query->get('referer')) : $this->generateUrl('dashboard_country_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
        }
    }
    
    /**
     * Change counrty status.
     *
     * @Route("/dashboard/country/change-status/{changeStatusTo}/{countryId}", name="dashboard_country_change_status", requirements={"changeStatusTo": "activate|deactivate", "countryId": "\d+"})
     * @Method({"GET"})
     *
     * @param  string                    $changeStatusTo
     * @param  int                       $countryId
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardCountryChangeStatusAction($changeStatusTo, $countryId, Request $request)
    {
        $changeStatusTo = ('deactivate' == $changeStatusTo) ? 0 : 1;
        $referer = $request->query->has('referer') ? urldecode($request->query->get('referer')) : $this->generateUrl('dashboard_country_list', [], UrlGeneratorInterface::ABSOLUTE_URL);
        
        if (! $this->dashboardCountryChangeStatus($countryId, $changeStatusTo)) {
            $this->addFlash('error', 'Status for selected country could not be changed successfully');
        } else {
            $this->addFlash('success', 'Status for selected country was changed successfully');
        }
        
        return $this->redirect($referer);
    }
    
    /**
     * Change multiple country status at a go.
     *
     * @Route("/dashboard/country/bulk-change-status", name="dashboard_country_bulk_status_change")
     * @Method({"POST"})
     *
     * @param  Request      $request
     *
     * @return JsonResponse
     */
    public function dashboardCountryBulkChangeStatusAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $countryIds = $request->request->get('countryIds');
            $countryIds = (false === strpos($countryIds, '-')) ? [$countryIds] : explode('-', $countryIds);
            $changeStatusTo = $request->request->get('changeStatusTo');
            $unaffectedCountryIds = [];
            
            foreach ($countryIds as $thisCountryId) {
                if (! $this->dashboardCountryChangeStatus($thisCountryId, $changeStatusTo)) {
                    $unaffectedCountryIds[] = $thisCountryId;
                }
            }
            
            if ($unaffectedCountryIds) {
                if (count($unaffectedCountryIds) == count($countryIds)) {
                    $this->addFlash('error', 'Status for selected countries could not be changed successfully');
                } else {
                    $this->addFlash('warning', 'Status for countries with ID: ' . implode(', ', $unaffectedCountryIds) . ' could not be changed');
                }
            } else {
                $this->addFlash('success', 'Status for selected countries were changed successfully');
            }
            
            return $this->json(['status' => 'complete']);
        }
    }
    
    /**
     * Changes status of a selected country.
     *
     * @param int  $countryId
     * @param int  $changeStatusTo
     *
     * @return bool
     */
    private function dashboardCountryChangeStatus($countryId, $changeStatusTo)
    {
        $em = $this->getDoctrine()->getManager();
        
        $country = $em->getRepository('NetFlexLocationBundle:Country')->findOneById($countryId);
        
        if (! $country) {
            return false;
        } else {
            $country->setStatus($changeStatusTo);
            $country->setLastModifiedOn(new \DateTime());
            $country->setLastModifiedBy($this->getUser()->getId());
            
            $em->persist($country);
            $em->flush();
            
            return true;
        }
    }
}
