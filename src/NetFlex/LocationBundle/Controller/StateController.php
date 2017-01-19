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
use NetFlex\LocationBundle\Entity\State;
use NetFlex\LocationBundle\Form\State\StateEditType;
use NetFlex\LocationBundle\Form\State\StateNewType;

class StateController extends Controller
{
    /**
     * Renders state list in dashboard.
     *
     * @Route("/dashboard/state/list/{page}/{sortColumn}/{sortOrder}", name="dashboard_state_list", defaults={"page":
     *      1, "sortColumn": "id", "sortOrder": "desc"}, requirements={"page": "\d+", "sortColumn":
     *     "id|country|name|status", "sortOrder": "asc|desc"})
     * @Method({"GET", "POST"})
     *
     * @param  int                       $page
     * @param  string                    $sortColumn
     * @param  string                    $sortOrder
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardStateListAction($page, $sortColumn, $sortOrder, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $paginationService = $this->get('pagination_service');
        
        $routeParameters = compact('page', 'sortColumn', 'sortOrder');
        
        $stateName = ($session->has('stateName')) ? $session->get('stateName') : null;
        $sortColumnFormatted = $this->getSortColumnFormatted($sortColumn);
        $sortOrderFormatted = strtoupper($sortOrder);
        
        $searchForm = $this->createFormBuilder()
        ->setAction($this->generateUrl('dashboard_state_list', [], UrlGeneratorInterface::ABSOLUTE_URL))
        ->setMethod('POST')
        ->add('stateName', TextType::class, [
            'data' => $stateName,
        ])
        ->getForm();
        
        $searchForm->handleRequest($request);
        
        if ($searchForm->isSubmitted()) {
            $formData = $searchForm->getData();
            
            $session->set('stateName', $formData['stateName']);
            
            return $this->redirectToRoute('dashboard_state_list');
        }
        
        $stateCount = $em->getRepository('NetFlexLocationBundle:State')->findStateCount($stateName,
            $sortColumnFormatted, $sortOrderFormatted);
    
        $limit = 10;
        $neighbor = 3;
        $offset = $paginationService->getRecordOffset($page, $limit);
        
        $states = $em->getRepository('NetFlexLocationBundle:State')->findState($offset, $limit, $stateName,
            $sortColumnFormatted, $sortOrderFormatted);
    
        $totalPageCount = $paginationService->getTotalPageCount($limit, $stateCount);
        $pageLinks = $paginationService->getPageLinks($page, $limit, $neighbor, $stateCount, $totalPageCount, 'dashboard_state_list', $routeParameters, []);
    
        $breadCrumbs = [
            [
                'title' => 'Dashboard Home',
                'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'title' => 'State List',
                'link' => $this->generateUrl('dashboard_state_list', $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL),
            ],
        ];
    
        return $this->render('NetFlexLocationBundle:State:list.html.twig', [
            'pageTitle' => 'State List',
            'breadCrumbs' => $breadCrumbs,
            'pageHeader' => '<h1>State<small> list </small></h1>',
            'listHeader' => 'State List',
            'searchForm' => $searchForm->createView(),
            'stateCount' => $stateCount,
            'totalPageCount' => $totalPageCount,
            'states' => $states,
            'pageLinks' => $pageLinks,
            'referer' => urlencode($this->generateUrl('dashboard_state_list', $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL)),
            'routeParameters' => $routeParameters,
            'allRouteParameters' => $routeParameters,
        ]);
    }
    
    /**
     * Exits from state search in dashboard.
     *
     * @Route("/dashboard/state/exit-search", name="dashboard_state_exit_search")
     * @Method({"GET"})
     *
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardStateExitSearchAction(Request $request)
    {
        $session = $request->getSession();
        
        $session->remove('stateName');
        
        return $this->redirectToRoute('dashboard_state_list');
    }
    
    /**
     * Renders state add page and also saves a state record.
     *
     * @Route("/dashboard/state/new", name="dashboard_state_new")
     * @Method({"GET", "POST"})
     *
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardStateNewAction(Request $request)
    {
        $state = new State();
        
        $form = $this->createForm(StateNewType::class, $state);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $currentDateTime = new \DateTime();
            $currentUser = $this->getUser()->getId();
    
            $state->setStatus(1);
            $state->setCreatedOn($currentDateTime);
            $state->setCreatedBy($currentUser);
            $state->setLastModifiedOn($currentDateTime);
            $state->setLastModifiedBy($currentUser);
            
            $em->persist($state);
            $em->flush();
    
            $this->addFlash('success', 'New state has been added successfully');
    
            return $this->redirectToRoute('dashboard_state_edit', ['stateId' => $state->getId()]);
        }
    
        $breadCrumbs = [
            [
                'title' => 'Dashboard Home',
                'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'title' => 'State List',
                'link' => $this->generateUrl('dashboard_state_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'title' => 'Add New State',
                'link' => $this->generateUrl('dashboard_state_new', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
        ];
    
        return $this->render('NetFlexLocationBundle:State:new.html.twig', [
            'pageTitle' => 'Add New State',
            'breadCrumbs' => $breadCrumbs,
            'pageHeader' => '<h1>State<small> new </small></h1>',
            'listHeader' => 'Add New State',
            'form' => $form->createView(),
            'referer' => ($request->query->has('referer')) ? urldecode($request->query->get('referer')) : $this->generateUrl('dashboard_state_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }
    
    /**
     * Renders state edit page and also updates state record.
     *
     * @Route("/dashboard/state/edit/{stateId}", name="dashboard_state_edit", requirements={"stateId": "\d+"})
     * @Method({"GET", "POST"})
     *
     * @param  int                       $stateId
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardStateEditAction($stateId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $state = $em->getRepository('NetFlexLocationBundle:State')->findOneBy(['id' => $stateId]);
        
        if (! $state) {
            throw $this->createNotFoundException('State was not found');
        } else {
            $form = $this->createForm(StateEditType::class, $state);
            
            $form->handleRequest($request);
            
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $state->setLastModifiedOn(new \DateTime());
                    $state->setLastModifiedBy($this->getUser()->getId());
    
                    $em->persist($state);
                    $em->flush();
    
                    $this->addFlash('success', 'State details has been updated successfully');
    
                    return $this->redirectToRoute('dashboard_state_edit', ['stateId' => $state->getId()]);
                }
            }
    
            $breadCrumbs = [
                [
                    'title' => 'Dashboard Home',
                    'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
                ],
                [
                    'title' => 'State List',
                    'link' => $this->generateUrl('dashboard_state_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
                ],
                [
                    'title' => 'Edit State',
                    'link' => $this->generateUrl('dashboard_state_edit', ['stateId' => $stateId],
                        UrlGeneratorInterface::ABSOLUTE_URL),
                ],
            ];
    
            return $this->render('NetFlexLocationBundle:State:edit.html.twig', [
                'pageTitle' => 'Edit State',
                'breadCrumbs' => $breadCrumbs,
                'pageHeader' => '<h1>State<small> edit </small></h1>',
                'listHeader' => 'Edit State',
                'state' => $state,
                'form' => $form->createView(),
                'referer' => ($request->query->has('referer')) ? urldecode($request->query->get('referer')) : $this->generateUrl('dashboard_state_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
        }
    }
    
    /**
     * Change state status.
     *
     * @Route("/dashboard/state/change-status/{changeStatusTo}/{stateId}", name="dashboard_state_change_status", requirements={"changeStatusTo": "activate|deactivate", "stateId": "\d+"})
     * @Method({"GET"})
     *
     * @param  string                    $changeStatusTo
     * @param  int                       $stateId
     * @param  Request                   $request
     *
     * @return Response|RedirectResponse
     */
    public function dashboardStateChangeStatusAction($changeStatusTo, $stateId, Request $request)
    {
        $changeStatusTo = ('deactivate' == $changeStatusTo) ? 0 : 1;
        $referer = $request->query->has('referer') ? urldecode($request->query->get('referer')) : $this->generateUrl('dashboard_state_list', [], UrlGeneratorInterface::ABSOLUTE_URL);
        
        if (! $this->dashboardStateChangeStatus($stateId, $changeStatusTo)) {
            $this->addFlash('error', 'Status for selected state could not be changed successfully');
        } else {
            $this->addFlash('success', 'Status for selected state was changed successfully');
        }
        
        return $this->redirect($referer);
    }
    
    /**
     * Change multiple state status at a go.
     *
     * @Route("/dashboard/state/bulk-change-status", name="dashboard_state_bulk_status_change")
     * @Method({"POST"})
     *
     * @param  Request      $request
     *
     * @return JsonResponse
     */
    public function dashboardStateBulkChangeStatusAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $stateIds = $request->request->get('stateIds');
            $stateIds = (false === strpos($stateIds, '-')) ? [$stateIds] : explode('-', $stateIds);
            $changeStatusTo = $request->request->get('changeStatusTo');
            $unaffectedStateIds = [];
            
            foreach ($stateIds as $thisStateId) {
                if (! $this->dashboardStateChangeStatus($thisStateId, $changeStatusTo)) {
                    $unaffectedStateIds[] = $thisStateId;
                }
            }
            
            if ($unaffectedStateIds) {
                if (count($unaffectedStateIds) == count($stateIds)) {
                    $this->addFlash('error', 'Status for selected states could not be changed successfully');
                } else {
                    $this->addFlash('warning', 'Status for states with ID: ' . implode(', ', $unaffectedStateIds) . ' could not be changed');
                }
            } else {
                $this->addFlash('success', 'Status for selected states were changed successfully');
            }
            
            return $this->json(['status' => 'complete']);
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
     * Changes status of a selected state.
     *
     * @param int  $stateId
     * @param int  $changeStatusTo
     *
     * @return bool
     */
    private function dashboardStateChangeStatus($stateId, $changeStatusTo)
    {
        $em = $this->getDoctrine()->getManager();
        
        $state = $em->getRepository('NetFlexLocationBundle:State')->findOneById($stateId);
        
        if (! $state) {
            return false;
        } else {
            $state->setStatus($changeStatusTo);
            $state->setLastModifiedOn(new \DateTime());
            $state->setLastModifiedBy($this->getUser()->getId());
            
            $em->persist($state);
            $em->flush();
            
            return true;
        }
    }
}
