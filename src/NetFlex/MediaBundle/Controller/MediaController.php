<?php

namespace NetFlex\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NetFlex\MediaBundle\Entity\Media;

/**
 * @Route("/dashboard/media")
 */
class MediaController extends Controller
{
    /**
     * Renders the media list page.
     *
     * @Route("/list/{page}/{sortColumn}/{sortOrder}", name="media_list", defaults={"page": 1, "sortColumn": "id", "sortOrder": "desc"}, requirements={"page": "\d+", "sortColumn": "id|name|type", "sortOrder": "asc|desc"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request A request instance
     *
     * @return Response A response instance
     */
    public function renderMediaListAction($page, $sortColumn, $sortOrder, Request $request)
    {
	    $mediaRepository = $this->getDoctrine()->getManager()->getRepository('NetFlexMediaBundle:Media');
	    $session = $request->getSession();
	    $paginationService = $this->get('pagination_service');
	
	    $routeParameters = [
		    'page' => $page,
		    'sortColumn' => $sortColumn,
		    'sortOrder' => $sortOrder,
	    ];
	    $routeExtraParameters = $request->query->all();
	
	    $mediaPaginationParams = $paginationService->getPaginationParameterValue('dashboard.media_list');
	
	    $limit = $mediaPaginationParams['record_per_page'];
	    $neighbor = $mediaPaginationParams['neighbor'];
	    $offset = $paginationService->getRecordOffset($page, $limit);
	
	    $sortColumn = $this->getSortColumn($sortColumn);
	    $sortOrder = strtoupper($sortOrder);
	    
	    $mediaName = (true === $session->has('mediaName')) ? $session->get('mediaName') : '';
	    $mediaExtension = (true === $session->has('mediaExtension')) ? $session->get('mediaExtension') : '';
	    
	    $searchForm = $this->createFormBuilder()
	            ->setAction($this->generateUrl('media_list', [], UrlGeneratorInterface::ABSOLUTE_URL))
		        ->setMethod('POST')
		        ->add('mediaName', TextType::class, [
		        	'empty_data' => '',
			        'data' => $mediaName,
		        ])
		        ->add('mediaExtension', ChoiceType::class, [
			        'placeholder' => '-All-',
			        'choices' => $this->getParameter('generic_media_search_types'),
			        'empty_data' => '',
			        'data' => $mediaExtension,
		        ])
		        ->getForm();
	
	    $searchForm->handleRequest($request);
	    
	    if (true === $searchForm->isSubmitted()) {
		    $searchData = $searchForm->getData();
		    
		    $session->set('mediaName', $searchData['mediaName']);
		    $session->set('mediaExtension', $searchData['mediaExtension']);
		    
		    return $this->redirectToRoute('media_list', array_merge($routeParameters, $routeExtraParameters));
	    }
	
	    $mediaCount = $mediaRepository->countMedias($sortColumn, $sortOrder, $mediaName, $mediaExtension);
	    
	    $totalPageCount = $paginationService->getTotalPageCount($limit, $mediaCount);
	    
	    $medias = $mediaRepository->getMedias($offset, $limit, $sortColumn, $sortOrder, $mediaName, $mediaExtension);
	
	    $pageLinks = $paginationService->getPageLinks($page, $limit, $neighbor, $mediaCount, $totalPageCount, 'media_list', $routeParameters, $routeExtraParameters);
	    
	    $breadCrumbs = [
		    [
			    'title' => 'Dashboard Home',
			    'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
		    [
		    	'title' => 'Media List',
			    'link' => $this->generateUrl('media_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
	    ];
	    
	    return $this->render('NetFlexMediaBundle:Media:list.html.twig', [
		    'pageTitle' => 'Media List',
		    'breadCrumbs' => $breadCrumbs,
		    'pageHeader' => '<h1>Media <small>list </small></h1>',
		    'listHeader' => 'Media List',
		    'searchForm' => $searchForm->createView(),
		    'mediaCount' => $mediaCount,
		    'totalPageCount' => $totalPageCount,
		    'medias' => $medias,
		    'pageLinks' => $pageLinks,
		    'referrer' => urlencode($this->generateUrl('media_list', array_merge($routeParameters, $routeExtraParameters), UrlGeneratorInterface::ABSOLUTE_URL)),
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
			case 'name':
				$sortColumn = 'M.mediaName';
				
				break;
			
			case 'type':
				$sortColumn = 'M.mediaExtension';
				
				break;
			
			case 'id':
			default:
				$sortColumn = 'M.id';
				
				break;
		}
		
		return $sortColumn;
	}
	
	/**
	 * Renders the multiple media upload page.
	 *
	 * @Route("/multi-media-upload", name="multi_media_upload")
	 * @Method({"GET"})
	 *
	 * @param  Request  $request A request instance
	 *
	 * @return Response          A response instance
	 */
    public function renderMultiMediaUploaderAction(Request $request)
    {
	    $mediaUploadDefaultOptions = $this->getParameter('media_upload_default_options');
	    $breadCrumbs = [
		    [
			    'title' => 'Dashboard Home',
			    'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
		    [
			    'title' => 'Media List',
			    'link' => $this->generateUrl('media_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
		    [
		    	'title' => 'Multiple Media Uploader',
			    'link' => $this->generateUrl('multi_media_upload', [], UrlGeneratorInterface::ABSOLUTE_URL)
		    ],
	    ];
	    $referrer = urldecode($request->query->get('ref'));
	
	    return $this->render('NetFlexMediaBundle:Media:multi_media_upload.html.twig', [
		    'pageTitle' => 'Upload Multiple Media',
		    'breadCrumbs' => $breadCrumbs,
		    'referrer' => $referrer,
		    'pageHeader' => '<h1>Upload <small>multiple media </small></h1>',
		    'mediaUploadDefaultOptions' => $mediaUploadDefaultOptions,
		    'multiMediaUploadAction' => $this->generateUrl('upload_multi_media', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    'maxMediaSize' => ini_get('upload_max_filesize'),
	    ]);
    }
	
	/**
	 * Uploads multiple medias.
	 *
	 * @Route("/upload-multi-media", name="upload_multi_media")
	 * @Method({"GET", "POST"})
	 *
	 * @param  Request  $request A request instance
	 *
	 * @return Response          A response instance
	 */
    public function uploadMultiMediaAction(Request $request)
    {
	    $mediaUploadDefaultOptions = $this->getParameter('media_upload_default_options');
	    $mediaUploadUserOptions = [
		    'script_url' => $this->generateUrl('delete_media', ['mode' => 'async'], UrlGeneratorInterface::ABSOLUTE_URL),
		    'media_upload_url' => $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/' . $this->getParameter('generic_media_upload_directory_name') . '/',
	    ];
	
	    $mediaUploadService = $this->get('multi_media_uploader');
	
	    $mediaUploadService->setUserOptions($mediaUploadUserOptions);
	
	    $medias = [];
	    
	    if ('post' === strtolower($request->server->get('REQUEST_METHOD'))) {
		    $contentDispositionHeader = $request->server->get('HTTP_CONTENT_DISPOSITION');
		    $mediaName = ($contentDispositionHeader) ? rawurldecode(preg_replace('/(^[^"]+")|("$)/', '', $contentDispositionHeader)) : null;
		
		    $contentRangeHeader = $request->server->get('HTTP_CONTENT_RANGE');
		    $contentRange = ($contentRangeHeader) ? preg_split('/[^0-9]+/', $contentRangeHeader)  : null;
		    $mediaSize = ($contentRange) ? $contentRange[3] : null;
		
		    $uploadedMedias = $request->files->get($mediaUploadDefaultOptions['param_name']);
		    
		    foreach ($uploadedMedias as $key => $thisUploadedMedia) {
			    $thisMedia = $mediaUploadService->uploadFile($thisUploadedMedia->getRealPath(), (($mediaName) ? $mediaName
				    : $thisUploadedMedia->getClientOriginalName()), (($mediaSize) ? $mediaSize :
				    $thisUploadedMedia->getClientSize()), $thisUploadedMedia->getMimeType(),
				    $thisUploadedMedia->getError(), $key, $contentRange);
			
			    $thisMediaName = substr($thisMedia->name, 0, strrpos($thisMedia->name, '.'));
			    $thisMediaExtension = str_replace($thisMediaName . '.', '', $thisMedia->name);
			
			    $newMedia = new Media();
			
			    $newMedia->setMediaName($thisMediaName);
			    $newMedia->setMediaExtension($thisMediaExtension);
			
			    $em = $this->getDoctrine()->getManager();
			    
			    $em->persist($newMedia);
			    $em->flush();
			
			    $mediaId = $newMedia->getId();
			
			    $deleteUrl = $thisMedia->deleteUrl;
			    $deleteUrl = preg_replace('/file=.+/', 'media_id=' . $mediaId, $deleteUrl);
			    $thisMedia->deleteUrl = $deleteUrl;
			
			    $medias[] = $thisMedia;
		    }
	    }
	
	    return $this->json([$mediaUploadDefaultOptions['param_name'] => $medias]);
    }
	
	/**
	 * Deletes a media.
	 *
	 * @Route("/delete-media/{mode}", name="delete_media", requirements={"mode": "async|sync"})
	 * @Method({"GET", "DELETE"})
	 *
	 * @param string  $mode    Asynchronous or synchronous operation
	 * @param Request $request A request instance
	 *
	 * @return JsonResponse|RedirectResponse
	 */
    public function deleteMediaAction($mode, Request $request)
	{
		$mediaId = $request->query->get('media_id');
		
		switch ($mode) {
			case 'async':
				$mediaDeletionResult = $this->deleteMedia($mediaId);
				
				return $this->json([$mediaDeletionResult['mediaName'] => $mediaDeletionResult['status']]);
				
				break;
			
			case 'sync':
				$allRouteParameters = $request->query->get('allRouteParameters');
				$pageIndex = (int) $allRouteParameters['page'];
				$selectedRecordCount = (int) $request->query->get('selectedRecordCount');
				$totalRecordOnPage = (int) $request->query->get('totalRecordOnPage');
				
				if (false === is_numeric($mediaId)) {
					$mediaIds = explode('-', $mediaId);
					$undeletedMediaIds = [];
					
					foreach ($mediaIds as $thisMediaId) {
						$mediaDeletionResult = $this->deleteMedia((int) $thisMediaId);
						
						if (false === $mediaDeletionResult['status']) {
							$undeletedMediaIds[] = $thisMediaId;
						}
					}
					
					if (empty($undeletedMediaIds)) {
						$this->addFlash('success', 'All medias were deleted successfully');
					} elseif (count($undeletedMediaIds) < count($mediaIds)) {
						$this->addFlash('warning', 'Medias with ID: ' . implode(', ', $undeletedMediaIds) . ' could not be deleted');
					} else {
						$this->addFlash('error', 'Medias could not be deleted');
					}
					
					$allRouteParameters['page'] = $this->adjustPageIndex($pageIndex, ($selectedRecordCount - count($undeletedMediaIds)), $totalRecordOnPage);
				} else {
					$mediaDeletionResult = $this->deleteMedia((int) $mediaId);
					
					if (false === $mediaDeletionResult['status']) {
						$this->addFlash('error', 'Media could not be deleted');
					} else {
						$this->addFlash('success', 'Media was deleted successfully');
					}
					
					$allRouteParameters['page'] = $this->adjustPageIndex($pageIndex, $selectedRecordCount, $totalRecordOnPage);
				}
				
				return $this->redirectToRoute('media_list', $allRouteParameters);
				
				break;
			
			default:
				break;
		}
	}
	
	/**
	 * Deletes a single media.
	 *
	 * @param int    $mediaId
	 * @param string $mode
	 *
	 * @return array
	 */
	protected function deleteMedia($mediaId)
	{
		$em = $this->getDoctrine()->getManager();
		$mediaRepository = $em->getRepository('NetFlexMediaBundle:Media');
		$mediaUploadService = $this->get('multi_media_uploader');
		
		$thisMedia = $mediaRepository->findOneById($mediaId);
		
		$mediaName = $thisMedia->getMediaName() . '.' . $thisMedia->getMediaExtension();
		
		if (true === $mediaUploadService->deleteFile($mediaName)) {
			$em->remove($thisMedia);
			$em->flush();
			
			return ['mediaName' => $mediaName, 'status' => true];
		}
		
		return ['mediaName' => $mediaName, 'status' => false];;
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
	 * @Route("/exit-from-search-mode", name="exit_from_search_mode")
	 *
	 * @param  Request $request
	 *
	 * @return RedirectResponse
	 */
	public function exitFromSearchModeAction(Request $request)
	{
		$session = $request->getSession();
		$referrer = $request->query->get('ref');
		
		$session->remove('mediaName');
		$session->remove('mediaExtension');
		
		return $this->redirect($referrer);
	}
}
