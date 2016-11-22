<?php

namespace NetFlex\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
	public function findUserById($userId)
	{
		$qb = $this->getEntityManager()->createQueryBuilder();
		
		return $qb->select('User, Address, Email, Contact')
				->from('NetFlexUserBundle:User', 'User')
				->leftJoin('User.addresses', 'Address')
				->leftJoin('User.emails', 'Email')
				->leftJoin('User.contacts', 'Contact')
				->where($qb->expr()->andX(
					$qb->expr()->eq('User.id', ':userId'),
					$qb->expr()->eq('User.status', 1),
					$qb->expr()->eq('Address.status', 1),
					$qb->expr()->eq('Email.status', 1),
					$qb->expr()->eq('Contact.status', 1)
				))
				->setParameter(':userId', $userId)
				->getQuery()
				->getOneOrNullResult();
	}
	
	public function findUserEncryptedPassword($userId)
	{
		$qb = $this->getEntityManager()->createQueryBuilder();
		
		$result = $qb->select('User.password')
					->from('NetFlexUserBundle:User', 'User')
					->where('User.id = :userId')
					->setParameter('userId', $userId)
					->getQuery()
					->getOneOrNullResult();
		
		return $result['password'];
	}
	
	public function findUsers($sortColumn, $sortOrder, $clientName, $offset = null, $limit = null)
	{
		$query = "SELECT U.id, U.username, CONCAT(U.first_name, IFNULL(CONCAT(' ', U.mid_name), ''), ' ', U.last_name) fullname, U.created_on FROM users U LEFT JOIN user_roles UR ON U.id = UR.user_id WHERE UR.role_id = 3 AND U.status = 1";
		
		if ($clientName) {
			$query .= " AND U.username LIKE '%" . $clientName . "%'";
		}
		
		$query .= " ORDER BY $sortColumn $sortOrder";
		
		if ((null !== $offset) && (null !== $limit)) {
			$query .= " LIMIT $offset, $limit";
		}
		
		$stmt = $this->getEntityManager()->getConnection()->prepare($query);
		
		$stmt->execute();
		
		return $stmt->fetchAll();
		
		
		
		/*$qb = $this->getEntityManager()->createQueryBuilder();
		
		$qb->select('User, Role')
			->from('NetFlexUserBundle:User', 'User')
			->leftJoin('User.roles', 'Role');
		
		if (! empty($clientName)) {
			$qb->where($qb->expr()->like($qb->expr()->concat('User.firstName', $qb->expr()->concat($qb->expr()->literal(' '), 'User.lastName')), $qb->expr()->literal("%$clientName%")));
			$qb->orWhere($qb->expr()->like('User.username', $qb->expr()->literal("%$clientName%")));
		}
		
		$qb->andWhere($qb->expr()->andX(
			$qb->expr()->eq('User.status', 1),
			$qb->expr()->eq('Role.status', 1)
		));
		
		$qb->orderBy($sortColumn, $sortOrder);
		
		if ((null !== $offset) && (null !== $limit)) {
			$qb->setFirstResult($offset);
			$qb->setMaxResults($limit);
		}
		
		$users = $qb->getQuery()->getResult();
		
		return $users;*/
	}
}
