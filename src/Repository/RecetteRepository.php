<?php

namespace App\Repository;

use App\Entity\Recette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recette|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recette|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recette[]    findAll()
 * @method Recette[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recette::class);
    }

    public function findPublicRecettes()
    {
        return $this->createQueryBuilder("r")
                    ->andWhere("r.public = true")
                    ->getQuery()
                    ->getResult()
                ;
    }

    public function findAllRecettesPaginated($criteria,$orderBy,$page,$limit,$user)
    {

        $queryBuilder = $this->createQueryBuilder("r");
        $queryBuilder->innerJoin("r.user", "u")
                    ->orWhere(
                        $queryBuilder->expr()->eq('r.public', true),
                        $user == null? null : $queryBuilder->expr()->eq('r.user', $user->getId())
                    );


//        foreach ($criteria as $key => $value) {
//            $queryBuilder->andWhere("r.{$key} = '{$value}'");
//        }

        foreach ($orderBy as $key => $value) {
            $queryBuilder->addOrderBy("r.{$key}",$value);
        }

        $query = $queryBuilder->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
        ;

        return $query->getResult();
        //return new Paginator($query);
    }



    // /**
    //  * @return Recette[] Returns an array of Recette objects
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
    public function findOneBySomeField($value): ?Recette
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
