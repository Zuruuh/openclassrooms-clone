<?php

namespace App\Repository;

use App\Entity\ResetUserPasswordToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ResetUserPasswordToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResetUserPasswordToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResetUserPasswordToken[]    findAll()
 * @method ResetUserPasswordToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResetUserPasswordTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetUserPasswordToken::class);
    }

    // /**
    //  * @return ResetUserPasswordToken[] Returns an array of ResetUserPasswordToken objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ResetUserPasswordToken
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
