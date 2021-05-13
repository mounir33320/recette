<?php

namespace App\Repository;

use App\Entity\Ingredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ingredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ingredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ingredient[]    findAll()
 * @method Ingredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */


class IngredientRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ingredient::class);
    }

    private function filter($query = null,$orderBy = null,$page = 1,$limit = 3)
    {
        $queryBuilder = $this->createQueryBuilder("i");

        if($query != null){
            $k = 0;
            foreach ($query as $queryWord) {
                $k++;
                $queryBuilder->andWhere("i.nom LIKE :nom$k")
                    ->setParameter("nom$k", "%$queryWord%");
            }
        }

        foreach ($orderBy as $key => $value) {
            $queryBuilder->addOrderBy("i.{$key}",$value);
        }

        return $queryBuilder
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ;
    }

    public function findAllIngredientsPaginated($query,$orderBy,$page,$limit)
    {
        return $this->filter($query,$orderBy,$page,$limit)
                            ->getQuery()
                            ->getResult();

    }

    // /**
    //  * @return Ingredient[] Returns an array of Ingredient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ingredient
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
