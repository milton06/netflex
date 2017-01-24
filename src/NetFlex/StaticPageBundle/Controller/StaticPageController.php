<?php

namespace NetFlex\StaticPageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NetFlex\StaticPageBundle\Entity\StaticPage;
use NetFlex\StaticPageBundle\Entity\StaticPageSection;
use NetFlex\StaticPageBundle\Form\StaticPageNewType;
use NetFlex\StaticPageBundle\Form\StaticPageEditType;
use NetFlex\StaticPageBundle\Form\StaticPageSearchType;

class StaticPageController extends Controller
{
    /**
     * Lists all static pages in dashboard.
     *
     * @Route("/dashboard/static-page/list/{page}/{sortColumn}/{sortOrder}", name="dashboard_static_page_list", defaults={"page": 1, "sortColumn": "id", "sortOrder": "desc"}, requirements={"page": "\d+", "sortColumn": "id|title|slug|status|created|modified", "sortOrder": "asc|desc"})
     * @Method({"GET", "POST"})
     *
     * @param  int                   $page
     * @param  string                $sortColumn
     * @param  string                $sortOrder
     * @param  Request               $request
     *
     * @return Response
     */
    public function dashboardStaticPageListAction($page, $sortColumn, $sortOrder, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $paginationService = $this->get('pagination_service');
        
        $searchTitle = (! $session->has('searchTitle')) ? '' : $session->get('searchTitle');
        $searchSlug = (! $session->has('searchSlug')) ? '' : $session->get('searchSlug');
        $searchStatus = (! $session->has('searchStatus')) ? '' : $session->get('searchStatus');
        $searchFromDate = (! $session->has('searchFromDate')) ? '' : $session->get('searchFromDate');
        $searchToDate = (! $session->has('searchToDate')) ? '' : $session->get('searchToDate');
        $searchFromDateFormatted = (! $searchFromDate) ? '' : \DateTime::createFromFormat('d/m/Y H:i:s', "$searchFromDate 00:00:00");
        $searchToDateFormatted = (! $searchToDate) ? '' : (\DateTime::createFromFormat('d/m/Y H:i:s', "$searchToDate 23:59:59"));
        $sortColumnFormatted = $this->getSortColumnFormatted($sortColumn);
        $sortOrderFormatted = strtoupper($sortOrder);
        
        $staticPageSearchForm = $this->createForm(StaticPageSearchType::class, null, [
            'action' => $this->generateUrl('dashboard_static_page_list', [
                'page' => 1,
                'sortColumn' => $sortColumn,
                'sortOrder' => $sortOrder
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'searchTitle' => $searchTitle,
            'searchSlug' => $searchSlug,
            'searchStatus' => $searchStatus,
            'searchStatuses' => $this->getParameter('inversed_static_page_statuses'),
            'searchFromDate' => $searchFromDate,
            'searchToDate' => $searchToDate,
        ]);
        
        $staticPageSearchForm->handleRequest($request);
        
        if ($staticPageSearchForm->isSubmitted()) {
            $formData = $staticPageSearchForm->getData();
            
            $session->set('searchTitle', $formData['searchTitle']);
            $session->set('searchSlug', $formData['searchSlug']);
            $session->set('searchStatus', $formData['searchStatus']);
            $session->set('searchFromDate', $formData['searchFromDate']);
            $session->set('searchToDate', $formData['searchToDate']);
            
            return $this->json([
                'status' => 'success',
                'redirectUrl' => $this->generateUrl('dashboard_static_page_list', [
                    'page' => 1,
                    'sortColumn' => $sortColumn,
                    'sortOrder' => $sortOrder
                ], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);
        }
        
        $staticPageCount = $em->getRepository('NetFlexStaticPageBundle:StaticPage')->countStaticPages($searchTitle, $searchSlug, $searchStatus, $searchFromDateFormatted, $searchToDateFormatted, $sortColumnFormatted, $sortOrderFormatted);
        
        $limit = 15;
        $offset = $paginationService->getRecordOffset($page, $limit);
        
        $staticPages = $em->getRepository('NetFlexStaticPageBundle:StaticPage')->findStaticPages($searchTitle, $searchSlug, $searchStatus, $searchFromDateFormatted, $searchToDateFormatted, $sortColumnFormatted, $sortOrderFormatted, $offset, $limit);
    
        $totalPageCount = $paginationService->getTotalPageCount($limit, $staticPageCount);
        $routeParameters = compact('page', 'sortColumn', 'sortOrder');
        
        $pageLinks = $paginationService->getPageLinks($page, $limit, 3, $staticPageCount, $totalPageCount, 'dashboard_static_page_list', $routeParameters, []);
    
        $viewData = [
            'pageTitle' => 'Static Page List',
            'breadCrumbs' => [
                [
                    'title' => 'Dashboard Home',
                    'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
                ],
                [
                    'title' => 'Static Pages',
                    'link' => $this->generateUrl('dashboard_static_page_list', $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL),
                ],
            ],
            'pageHeader' => 'Static Page <small>list </small>',
            'panelHeader' => 'Static Page List',
            'referer' => urlencode($this->generateUrl('dashboard_static_page_list', $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL)),
            'staticPages' => $staticPages,
            'staticPageStatuses' => $this->getParameter('static_page_statuses'),
            'totalPageCount' => $totalPageCount,
            'routeParameters' => $routeParameters,
            'pageLinks' => $pageLinks,
            'staticPageSearchForm' => $staticPageSearchForm->createView(),
        ];
    
        return $this->render('NetFlexStaticPageBundle:StaticPage:list.html.twig', $viewData);
    }
    
    /**
     * Gets respective entity property name to sort by.
     *
     * @param $sortColumn
     *
     * @return string
     */
    private function getSortColumnFormatted($sortColumn)
    {
        switch ($sortColumn) {
            case 'created':
                return 'createdOn';
                
                break;
            
            case 'modified':
                return 'lastModifiedOn';
                
                break;
                
            default:
                return $sortColumn;
                
                break;
        }
    }
    
    /**
     * Exits from static page search mode in dashboard.
     *
     * @Route("/dashboard/static-page/exit-search", name="dashboard_static_page_exit_search")
     * @Method({"POST"})
     *
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function dashboardStaticPageExitSearchAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $session = $request->getSession();
    
            $session->remove('searchTitle');
            $session->remove('searchSlug');
            $session->remove('searchStatus');
            $session->remove('searchFromDate');
            $session->remove('searchToDate');
            
            return $this->json(['status' => 'success', 'redirectUrl' => $this->generateUrl('dashboard_static_page_list', [], UrlGeneratorInterface::ABSOLUTE_URL)]);
        }
    }
    
    /**
     * Adds a new static page from dashboard.
     *
     * @Route("/dashboard/static-page/new", name="dashboard_static_page_new")
     * @Method({"GET", "POST"})
     *
     * @param  Request               $request
     *
     * @return Response|JsonResponse
     */
    public function dashboardStaticPageNewAction(Request $request)
    {
        $staticPage = new StaticPage();
        $staticPage->addStaticPageSection(new StaticPageSection());
        
        $staticPageNewForm = $this->createForm(StaticPageNewType::class, $staticPage);
    
        $staticPageNewForm->handleRequest($request);
        
        if ($staticPageNewForm->isSubmitted()) {
            $validationErrors = $this->get('validator')->validate($staticPage);
            
            if (0 < count($validationErrors)) {
                $errorMessages = [];
                
                foreach ($validationErrors as $thisValidationError) {
                    $key = (! in_array($thisValidationError->getPropertyPath(), ['title', 'slug'])) ? $staticPageNewForm->getName() . '_' . str_replace(['[', ']', '.'], ['_', '_', ''], $thisValidationError->getPropertyPath()) : $thisValidationError->getPropertyPath();
                    
                    $errorMessages[$key] = $thisValidationError->getMessage();
                }
                
                return $this->json(['status' => 'validationError', 'errorMessages' => $errorMessages]);
            } else {
                $em = $this->getDoctrine()->getManager();
    
                foreach ($staticPage->getStaticPageSections() as $thisStaticPageSection) {
                    $thisStaticPageSection->setStatus(1);
                    $staticPage->addStaticPageSection($thisStaticPageSection);
                }
    
                $currentDateTime = new \DateTime();
                $staticPage->setStatus(2);
                $staticPage->setCreatedOn($currentDateTime);
                $staticPage->setCreatedBy(1);
                $staticPage->setLastModifiedOn($currentDateTime);
                $staticPage->setLastModifiedBy(1);
    
                $em->persist($staticPage);
                $em->flush();
    
                return $this->json(['status' => 'success', 'id' => $staticPage->getId()]);
            }
        } else {
            $viewData = [
                'pageTitle' => 'Add New Static Page',
                'breadCrumbs' => [
                    [
                        'title' => 'Dashboard Home',
                        'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    ],
                    [
                        'title' => 'Static Pages',
                        'link' => $this->generateUrl('dashboard_static_page_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    ],
                    [
                        'title' => 'Add New Static Page',
                        'link' => $this->generateUrl('dashboard_static_page_new', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    ],
                ],
                'pageHeader' => '<h1>Static Page <small>add new </small></h1>',
                'panelHeader' => 'Add New Static Page',
                'staticPageNewForm' => $staticPageNewForm->createView(),
                'referer' => ($request->query->has('referer')) ? urldecode($request->query->get('referer')) : $this->generateUrl('dashboard_static_page_list', [
                    'page' => 1,
                    'sortColumn' => 'id',
                    'sortOrder' => 'desc',
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ];
    
            return $this->render('NetFlexStaticPageBundle:StaticPage:new.html.twig', $viewData);
        }
    }
    
    /**
     * Updates a static page from dashboard.
     *
     * @Route("/dashboard/static-page/edit/{id}", name="dashboard_static_page_edit")
     * @Method({"GET", "POST"})
     *
     * @param  int                   $id
     * @param  Request               $request
     *
     * @return Response|JsonResponse
     */
    public function dashboardStaticPageEditAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $staticPage = $em->getRepository('NetFlexStaticPageBundle:StaticPage')->findAStaticPageById($id);
        
        if (! $staticPage) {
            throw new $this->createNotFoundException("Static page with ID $id does not exist");
        } else {
            $staticPageEditForm = $this->createForm(StaticPageEditType::class, $staticPage);
            
            $staticPageEditForm->handleRequest($request);
            
            if ($staticPageEditForm->isSubmitted()) {
                $validationErrors = $this->get('validator')->validate($staticPage);
    
                if (0 < count($validationErrors)) {
                    $errorMessages = [];
        
                    foreach ($validationErrors as $thisValidationError) {
                        $key = (! in_array($thisValidationError->getPropertyPath(), ['title', 'slug'])) ? $staticPageEditForm->getName() . '_' . str_replace(['[', ']', '.'], ['_', '_', ''], $thisValidationError->getPropertyPath()) : $thisValidationError->getPropertyPath();
            
                        $errorMessages[$key] = $thisValidationError->getMessage();
                    }
        
                    return $this->json(['status' => 'validationError', 'errorMessages' => $errorMessages]);
                } else {
                    $em = $this->getDoctrine()->getManager();
    
                    foreach ($staticPage->getStaticPageSections() as $thisStaticPageSection) {
                        if (! $thisStaticPageSection->getId()) {
                            $thisStaticPageSection->setStatus(1);
                            $staticPage->addStaticPageSection($thisStaticPageSection);
                        }
                    }
    
                    $staticPage->setLastModifiedOn(new \DateTime());
                    $staticPage->setLastModifiedBy(1);
    
                    $em->persist($staticPage);
                    $em->flush();
    
                    return $this->json(['status' => 'success', 'id' => $staticPage->getId()]);
                }
            } else {
                $viewData = [
                    'pageTitle' => 'Edit Static Page',
                    'breadCrumbs' => [
                        [
                            'title' => 'Dashboard Home',
                            'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        ],
                        [
                            'title' => 'Static Pages',
                            'link' => $this->generateUrl('dashboard_static_page_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        ],
                        [
                            'title' => 'Edit Static Page',
                            'link' => $this->generateUrl('dashboard_static_page_edit', ['id' => $staticPage->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                        ],
                    ],
                    'pageHeader' => 'Static Page <small>edit </small>',
                    'panelHeader' => 'Edit Static Page',
                    'staticPage' => $staticPage,
                    'staticPageEditForm' => $staticPageEditForm->createView(),
                    'referer' => ($request->query->has('referer')) ? urldecode($request->query->get('referer')) : $this->generateUrl('dashboard_static_page_list', [
                        'page' => 1,
                        'sortColumn' => 'id',
                        'sortOrder' => 'desc',
                    ], UrlGeneratorInterface::ABSOLUTE_URL),
                ];
    
                return $this->render('NetFlexStaticPageBundle:StaticPage:edit.html.twig', $viewData);
            }
        }
    }
    
    /**
     * Displays a static page details from dashboard.
     *
     * @Route("/dashboard/static-page/view/{id}", name="dashboard_static_page_view")
     * @Method({"GET"})
     *
     * @param  int        $id
     * @param  Request $request
     *
     * @return Response
     */
    public function dashboardStaticPageViewAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $staticPage = $em->getRepository('NetFlexStaticPageBundle:StaticPage')->findAStaticPageById($id);
        
        if (! $staticPage) {
            throw new $this->createNotFoundException("Static page with ID $id does not exist");
        } else {
            $viewData = [
                'pageTitle' => 'Static Page Details',
                'breadCrumbs' => [
                    [
                        'title' => 'Dashboard Home',
                        'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    ],
                    [
                        'title' => 'Static Pages',
                        'link' => $this->generateUrl('dashboard_static_page_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    ],
                    [
                        'title' => 'Static Page Details',
                        'link' => $this->generateUrl('dashboard_static_page_view', ['id' => $staticPage->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                    ],
                ],
                'pageHeader' => 'Static Page <small>details </small>',
                'panelHeader' => 'Static Page Details',
                'staticPage' => $staticPage,
                'referer' => ($request->query->has('referer')) ? urldecode($request->query->get('referer')) : $this->generateUrl('dashboard_static_page_list', [
                    'page' => 1,
                    'sortColumn' => 'id',
                    'sortOrder' => 'desc',
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ];
    
            return $this->render('NetFlexStaticPageBundle:StaticPage:view.html.twig', $viewData);
        }
    }
    
    /**
     * Publishes a static page from dashboard.
     *
     * @Route("/dashboard/static-page/publish/{id}", name="dashboard_static_page_publish", requirements={"id": "\d+"})
     * @Method({"POST"})
     *
     * @param  string        $id
     * @param  Request       $request
     *
     * @return JsonResponse
     */
    public function dashboardStaticPagePublishAction($id, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            if (! $this->changeStaticPageStatus($id, 'publish')) {
                return $this->json(['status' => 'publishError', 'message' => ' Selected static page couldn\'t be published']);
            } else {
                return $this->json(['status' => 'publishSuccess', 'message' => ' Selected static page was published successfully.']);
            }
        }
    }
    
    /**
     * Publishes multiple static pages from dashboard.
     *
     * @Route("/dashboard/static-page/bulk-publish/{ids}", name="dashboard_static_page_bulk_publish", requirements={"ids": "[\d-]+"})
     * @Method({"POST"})
     *
     * @param  string        $ids
     * @param  Request       $request
     *
     * @return JsonResponse
     */
    public function dashboardStaticPageBulkPublishAction($ids, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $selectedStaticPageIds = (false === strpos($ids, '-')) ? [$ids] : explode('-', $ids);
            $unPublishedStaticPageIds = [];
            
            foreach ($selectedStaticPageIds as $thisSelectedStaticPageId) {
                if (! $this->changeStaticPageStatus($thisSelectedStaticPageId, 'publish')) {
                    $unPublishedStaticPageIds[] = $thisSelectedStaticPageId;
                }
            }
            
            if (! $unPublishedStaticPageIds) {
                return $this->json(['status' => 'publishSuccess', 'message' => ' Selected static pages were published successfully.']);
            } else {
                if (count($selectedStaticPageIds) == count($unPublishedStaticPageIds)) {
                    return $this->json(['status' => 'publishError', 'message' => ' Selected static pages couldn\'t be published.']);
                } else {
                    return $this->json(['status' => 'publishWarning', 'message' => ' Static pages with IDs ' . implode(', ', $unPublishedStaticPageIds) . ' couldn\'t be published']);
                }
            }
        }
    }
    
    /**
     * Trashes a static page from dashboard.
     *
     * @Route("/dashboard/static-page/trash/{id}", name="dashboard_static_page_trash", requirements={"id": "\d+"})
     * @Method({"POST"})
     *
     * @param  string        $id
     * @param  Request       $request
     *
     * @return JsonResponse
     */
    public function dashboardStaticPageTrashAction($id, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            if (! $this->changeStaticPageStatus($id, 'trash')) {
                return $this->json(['status' => 'trashError', 'message' => ' Selected static page couldn\'t be trashed']);
            } else {
                return $this->json(['status' => 'trashSuccess', 'message' => ' Selected static page was trashed successfully.']);
            }
        }
    }
    
    /**
     * Trashes multiple static pages from dashboard.
     *
     * @Route("/dashboard/static-page/bulk-trash/{ids}", name="dashboard_static_page_bulk_trash", requirements={"ids": "[\d-]+"})
     * @Method({"POST"})
     *
     * @param  string        $ids
     * @param  Request       $request
     *
     * @return JsonResponse
     */
    public function dashboardStaticPageBulkTrashAction($ids, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $selectedStaticPageIds = (false === strpos($ids, '-')) ? [$ids] : explode('-', $ids);
            $unTrashedStaticPageIds = [];
            
            foreach ($selectedStaticPageIds as $thisSelectedStaticPageId) {
                if (! $this->changeStaticPageStatus($thisSelectedStaticPageId, 'trash')) {
                    $unTrashedStaticPageIds[] = $thisSelectedStaticPageId;
                }
            }
    
            if (! $unTrashedStaticPageIds) {
                return $this->json(['status' => 'trashSuccess', 'message' => ' Selected static pages were trashed successfully.']);
            } else {
                if (count($selectedStaticPageIds) == count($unTrashedStaticPageIds)) {
                    return $this->json(['status' => 'trashError', 'message' => ' Selected static pages couldn\'t be trashed.']);
                } else {
                    return $this->json(['status' => 'trashWarning', 'message' => ' Static pages with IDs ' . implode(', ', $unTrashedStaticPageIds) . ' couldn\'t be trashed']);
                }
            }
        }
    }
    
    /**
     * Deletes a static page from dashboard.
     *
     * @Route("/dashboard/static-page/delete/{id}/{selectedRecordCount}/{recordOnPage}/{currentPage}", name="dashboard_static_page_delete", requirements={"id": "\d+", "selectedRecordCount": "1", "recordOnPage": "\d+", "currentPage": "\d+"})
     * @Method({"POST"})
     *
     * @param  string        $id
     * @param  int           $selectedRecordCount
     * @param  int           $recordOnPage
     * @param  int           $currentPage
     * @param  Request       $request
     *
     * @return JsonResponse
     */
    public function dashboardStaticPageDeleteAction($id, $selectedRecordCount, $recordOnPage, $currentPage, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            if (! $this->changeStaticPageStatus($id, 'delete')) {
                return $this->json(['status' => 'deleteError', 'message' => ' Selected static page couldn\'t be deleted']);
            } else {
                $currentPage = $this->setCurrentPage($selectedRecordCount, $recordOnPage, $currentPage);
    
                return $this->json(['status' => 'deleteSuccess', 'message' => ' Selected static page was deleted successfully.', 'page' => $currentPage]);
            }
        }
    }
    
    /**
     * Deletes multiple static pages from dashboard.
     *
     * @Route("/dashboard/static-page/bulk-delete/{ids}/{selectedRecordCount}/{recordOnPage}/{currentPage}", name="dashboard_static_page_bulk_delete", requirements={"ids": "[\d-]+", "selectedRecordCount": "\d+", "recordOnPage": "\d+", "currentPage": "\d+"})
     * @Method({"POST"})
     *
     * @param  string        $ids
     * @param  int           $selectedRecordCount
     * @param  int           $recordOnPage
     * @param  int           $currentPage
     * @param  Request       $request
     *
     * @return JsonResponse
     */
    public function dashboardStaticPageBulkDeleteAction($ids, $selectedRecordCount, $recordOnPage, $currentPage, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $selectedStaticPageIds = (false === strpos($ids, '-')) ? [$ids] : expolde('-', $ids);
            $undeletedStaticPageIds = [];
    
            foreach ($selectedStaticPageIds as $thisSelectedStaticPageId) {
                if (! $this->changeStaticPageStatus($thisSelectedStaticPageId, 'delete')) {
                    $undeletedStaticPageIds[] = $thisSelectedStaticPageId;
                }
            }
    
            if (! $undeletedStaticPageIds) {
                $currentPage = $this->setCurrentPage($selectedRecordCount, $recordOnPage, $currentPage);
        
                return $this->json(['status' => 'deleteSuccess', 'message' => ' Selected static pages were deleted successfully.', 'page' => $currentPage]);
            } else {
                if (count($selectedStaticPageIds) == count($undeletedStaticPageIds)) {
                    return $this->json(['status' => 'deleteError', 'message' => ' Selected static pages couldn\'t be deleted.']);
                } else {
                    return $this->json(['status' => 'deleteWarning', 'message' => ' Static pages with IDs ' . implode(', ', $undeletedStaticPageIds) . ' couldn\'t be deleted']);
                }
            }
        }
    }
    
    /**
     * Changes a static page status.
     *
     * @param  int  $selectedStaticPageId
     *
     * @return bool
     */
    private function changeStaticPageStatus($selectedStaticPageId, $mode)
    {
        $em = $this->getDoctrine()->getManager();
        
        $selectedStaticPage = $em->getRepository('NetFlexStaticPageBundle:StaticPage')->findAStaticPageById($selectedStaticPageId);
        
        if (! $selectedStaticPage) {
            return false;
        } else {
            switch ($mode) {
                case 'publish':
                    $selectedStaticPage->setStatus(2);
                    
                    break;
                    
                case 'trash':
                    $selectedStaticPage->setStatus(1);
                    
                    break;
                    
                case 'delete':
                    $selectedStaticPage->setStatus(0);
                    
                    break;
                    
                default:
                    break;
            }
            
            $selectedStaticPage->setLastModifiedOn(new \DateTime());
            $selectedStaticPage->setLastModifiedBy(1);
            
            $em->persist($selectedStaticPage);
            $em->flush();
            
            return true;
        }
    }
    
    /**
     * Adjusts current page-index after static page deletion.
     *
     * @param  int   $selectedRecordCount
     * @param  int   $recordOnPage
     * @param  int   $currentPage
     *
     * @return int
     */
    private function setCurrentPage($selectedRecordCount, $recordOnPage, $currentPage)
    {
        if ((1 < $currentPage) && ($selectedRecordCount == $recordOnPage)) {
            return ($currentPage - 1);
        } else {
            return $currentPage;
        }
    }
    
    /**
     * Returns autocomplete data in dashboard.
     *
     * @Route("/dashboard/static-page/autocomplete", name="dashboard_static_page_autocomplete")
     * @Method({"GET"})
     *
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function dashboardStaticPageAutocompleteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $search = ($request->query->has('search')) ? trim($request->query->get('search')) : '';
            $term = ($request->query->has('term')) ? trim($request->query->get('term')) : '';
    
            $em = $this->getDoctrine()->getManager();
            
            $autocompleteData = [];
            
            if ('title' == $search) {
                $staticPages = $em->getRepository('NetFlexStaticPageBundle:StaticPage')->findStaticPagesByTitle($term);
                
                foreach ($staticPages as $thisStaticPage) {
                    $autocompleteData[] = $thisStaticPage->getTitle();
                }
            } elseif ('slug' == $search) {
                $staticPages = $em->getRepository('NetFlexStaticPageBundle:StaticPage')->findStaticPagesBySlug($term);
    
                foreach ($staticPages as $thisStaticPage) {
                    $autocompleteData[] = $thisStaticPage->getSlug();
                }
            } else {
                //
            }
            
            return $this->json(['autocompleteData' => $autocompleteData]);
        }
    }
}
