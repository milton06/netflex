<?php

namespace NetFlex\LocationBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * StateRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StateRepository extends EntityRepository
{
	public function findStatesInACountryByName($countryName, $stateHint)
	{
		$qb = $this->getEntityManager()->createQueryBuilder();
		
		return $qb->select('partial S.{id, name}')
			->from('NetFlexLocationBundle:State', 'S')
			->innerJoin('S.countryId', 'C')
			->where($qb->expr()->andX(
				$qb->expr()->eq('C.name', ':countryName'),
				$qb->expr()->like('S.name', $qb->expr()->literal("%$stateHint%")),
				$qb->expr()->eq('S.status', 1)
			))
			->setParameter('countryName', $countryName)
			->getQuery()
			->getResult();
	}
}
