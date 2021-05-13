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

    private function filter($query = null,$orderBy = null,$page = 1,$limit = 3)
    {
        $queryBuilder = $this->createQueryBuilder("r");

        if($query != null){
            $k = 0;
            foreach ($query as $queryWord) {
                $k++;
                $queryBuilder->andWhere("r.nom LIKE :nom$k")
                    ->setParameter("nom$k", "%$queryWord%");
            }
        }

        foreach ($orderBy as $key => $value) {
            $queryBuilder->addOrderBy("r.{$key}",$value);
        }

        return $queryBuilder
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
        ;
    }

    public function findAllRecettesPaginated($query,$orderBy,$page,$limit,$currentUser)
    {
        $queryBuilder = $this->filter($query,$orderBy,$page,$limit);

        $queryBuilder
            /*->andWhere("r.public = true")
            ->orWhere("r.user = " .$user->getId() );*/

            ->innerJoin("r.user", "u")
            ->orWhere(
                $queryBuilder->expr()->eq('r.public', true),
                $currentUser == null? null : $queryBuilder->expr()->eq('r.user', $currentUser->getId()))
            ->andWhere("u.actif = true");
            //->andWhere(":user MEMBER OF r.user")->setParameter("user", $currentUser);

        $sqlQuery = $queryBuilder->getQuery();

        return $sqlQuery->getResult();
        //return new Paginator($sqlQuery);
    }

    public function findRecettesByUser($query,$orderBy,$page,$limit,$user,$currentUser)
    {
        $queryBuilder = $this->filter($query,$orderBy,$page,$limit)
            //->andWhere("r.user = " . $user->getId());
            ->andWhere(":user MEMBER OF r.user")
            ->setParameter("user", $user);

        if ($user != $currentUser) {
            $queryBuilder->andWhere("r.public = true");
        }

        $sqlQuery = $queryBuilder->getQuery();

        return $sqlQuery->getResult();
    }

    public function findRecettesByCategorie($query,$orderBy,$page,$limit,$categorie)
    {
        $queryBuilder = $this->filter($query,$orderBy,$page,$limit);

        $queryBuilder->andWhere(":categorie MEMBER OF r.categories")
                    ->setParameter("categorie", $categorie);


        $sqlQuery = $queryBuilder->getQuery();

        return $sqlQuery->getResult();
    }

    public function findRecettesByIngredient($query,$orderBy,$page,$limit,$ingredient)
    {
        $queryBuilder = $this->filter($query,$orderBy,$page,$limit);

        $queryBuilder->andWhere(":ingredients MEMBER OF r.ingredients")
            ->setParameter("ingredients", $ingredient);


        $sqlQuery = $queryBuilder->getQuery();

        return $sqlQuery->getResult();
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
