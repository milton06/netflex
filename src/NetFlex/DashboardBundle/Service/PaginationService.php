<?php
namespace NetFlex\DashboardBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaginationService
{
	private $paginationParameters;
	private $router;
	
	public function __construct($paginationParameters, Router $router)
	{
		$this->paginationParameters = $paginationParameters;
		$this->router = $router;
	}
	
	/**
	 * @param int $pageIndex
	 * @param int $limit
	 *
	 * @return int
	 */
	public function getRecordOffset($pageIndex, $limit)
	{
		/**
		 * If page index is 1 (i.e. the first page), record offset has to be 0.
		 * Other wise record starts at calculated offset.
		 */
		return (1 === (int) $pageIndex) ? 0 : ($limit * ($pageIndex - 1));
	}
	
	/**
	 * Gets total page count.
	 *
	 * @param int $limit
	 * @param int $totalRecordCount
	 *
	 * @return int
	 */
	public function getTotalPageCount($limit, $totalRecordCount)
	{
		return (int) ceil(($totalRecordCount / $limit));
	}
	
	/**
	 * Gets pagination links.
	 *
	 * @param int    $pageIndex
	 * @param int    $limit
	 * @param int    $neighbor
	 * @param int    $totalRecordCount
	 * @param int    $totalPageCount
	 * @param string $routeName
	 * @param array  $routeParameters
	 * @param array  $routeExtraParameters
	 *
	 * @return array
	 */
	public function getPageLinks($pageIndex, $limit, $neighbor, $totalRecordCount, $totalPageCount, $routeName, $routeParameters, $routeExtraParameters)
	{
		$pageIndex = (int) $pageIndex;
		$pageLinks = [];
		
		if (0 < $totalRecordCount) {
			$start = 1;
			$end = ((2 * $neighbor) + 1);
			
			if (1 === $pageIndex) {
				$end = ($totalPageCount < $end) ? $totalPageCount : $end;
			} elseif ($totalPageCount === $pageIndex) {
				$start = (($totalPageCount - (2 * $neighbor)) <= 0) ? 1 : ($totalPageCount - (2 * $neighbor));
				$end = $totalPageCount;
			} else {
				$start = (($pageIndex - $neighbor) <= 0) ? 1 : ($pageIndex - $neighbor);
				$end = ($totalPageCount <= ($pageIndex + $neighbor)) ? $totalPageCount : ($pageIndex + $neighbor);
				
				if (($end - $start) < (2 * $neighbor)) {
					$extraRequired = ((2 * $neighbor) - ($end - $start));
					
					if (1 <= ($start - $extraRequired)) {
						$start = ($start - $extraRequired);
					} elseif (($end + $extraRequired) <= $totalPageCount) {
						$end = ($end + $extraRequired);
					} else {
						//
					}
				}
			}
			
			if (1 === $pageIndex) {
				$pageLinks['first'] = 'javascript:void(0);';
			} else {
				$pageLinks['first'] = $this->getPaginationUrl(1, $routeName, $routeParameters, $routeExtraParameters);
			}
			
			$pageLinks['previous'] = ((1 === $pageIndex) ? 'javascript:void(0)' : $this->getPaginationUrl(($pageIndex - 1), $routeName, $routeParameters, $routeExtraParameters));
			
			for ($i = $start; $i <= $end; $i++) {
				$pageLinks['links'][$i] = $this->getPaginationUrl($i, $routeName, $routeParameters, $routeExtraParameters);
			}
			
			$pageLinks['next'] = (($totalPageCount === $pageIndex) ? 'javascript:void(0)' : $this->getPaginationUrl(($pageIndex + 1), $routeName, $routeParameters, $routeExtraParameters));
			
			if ($pageIndex === $totalPageCount) {
				$pageLinks['last'] = 'javascript:void(0);';
			} else {
				$pageLinks['last'] = $this->getPaginationUrl($totalPageCount, $routeName, $routeParameters, $routeExtraParameters);
			}
		}
		
		return $pageLinks;
	}
	
	/**
	 * @param int $pageIndex
	 * @param string $routeName
	 * @param array $routeParameters
	 * @param array $routeExtraParameters
	 *
	 * @return string
	 */
	protected function getPaginationUrl($pageIndex, $routeName, $routeParameters, $routeExtraParameters)
	{
		$routeParameters['page'] = $pageIndex;
		$routeParameters = array_merge($routeParameters, $routeExtraParameters);
		
		return $this->router->generate($routeName, $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL);
	}
	
	/**
	 * Gets a pagination parameter value.
	 *
	 * @param string $parameterKey A '.' separated string of parameter keys
	 *
	 * @return mixed
	 */
	public function getPaginationParameterValue($parameterKey = '')
	{
		if (empty($parameterKey)) {
			/**
			 * No parameter key was passed as argument. So return the entire array.
			 */
			return $this->paginationParameters;
		}
		
		/**
		 * Get rid of any surrounding accidental white space.
		 */
		$parameterKey = trim($parameterKey);
		
		if (false === strpos($parameterKey, '.')) {
			/**
			 * Only a first level parameter key (dashboard/front) is passed.
			 */
			if (false === array_key_exists($parameterKey, $this->paginationParameters)) {
				/**
				 * Invalid parameter key.
				 */
				throw new \InvalidArgumentException("Invalid pagination parameter key: $parameterKey.");
			}
			
			return $this->paginationParameters[$parameterKey];
		}
		
		/**
		 * Split the parameter keys.
		 */
		$parameterKeys = explode('.', $parameterKey);
		
		/**
		 * Set the parameter value to the entire parameters array.
		 */
		$parameterValue = $this->paginationParameters;
		
		/**
		 * Traverse.
		 */
		array_filter($parameterKeys, function($key) use(&$parameterValue) {
			/**
			 * Get rid of any surrounding accidental white space.
			 */
			$key = trim($key);
			
			/**
			 * Invalid parameter key.
			 */
			if (false === array_key_exists($key, $parameterValue)) {
				throw new \InvalidArgumentException("Invalid pagination parameter key: $key");
			}
			
			/**
			 * Set the parameter value to the inner array.
			 */
			$parameterValue = $parameterValue[$key];
		});
		
		return $parameterValue;
	}
}
