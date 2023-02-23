<?php

namespace App\Repository;

use App\Entity\Paiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Paiement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paiement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paiement[]    findAll()
 * @method Paiement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Paiement $entity, bool $flush = true): void
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
    public function remove(Paiement $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
      * @return Paiement[] Returns an array of Paiement objects
    */
    public function findByDateDesc()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.deleted = :deleted')
            ->setParameter('deleted', 0)
            ->orderBy('p.created', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function countByCritere($critere)
    {
        $alias = $critere . 's';
        $group = 'group' . $critere;

        $query = $this->createQueryBuilder('a')
            ->select("a.$critere as $alias, COUNT(a.$critere) as $group")
            ->groupBy("$alias")
        ;

        return $query->getQuery()->getResult();
    }

    public function findByLimit($limit = null)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.created', 'DESC')
            ->setMaxResults($limit ? $limit : 99999999)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByUser($user, $limit = null)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.created', 'DESC')
            ->andWhere('p.user = :user')
            ->setMaxResults($limit ? $limit : 99999999)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Paiement[] Returns an array of Paiement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Paiement
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
