<?php

namespace App\Repository;

use App\Entity\Mission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mission[]    findAll()
 * @method Mission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mission::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Mission $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Mission $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
    * @return Mission[] Returns an array of Mission objects
    */
    public function findByDateDesc($limit = null)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.statut = 1')
            ->orderBy('m.created', 'DESC')
            ->setMaxResults($limit ? $limit : 99999999)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByUser(User $user, $limit = null)
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.created', 'DESC')
            ->andWhere('m.user = :user')
            ->setMaxResults($limit ? $limit : 99999999)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByLimit($limit = null)
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.created', 'DESC')
            ->setMaxResults($limit ? $limit : 99999999)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLastByUser(User $user)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.statut = 1')
            ->andWhere('m.user = :user')
            ->setParameter('user', $user)
            ->orderBy('m.updated', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        ;
    }

    public function findLastCompleteByUser(User $user)
    {
        return $this->createQueryBuilder('m')
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Mission
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
