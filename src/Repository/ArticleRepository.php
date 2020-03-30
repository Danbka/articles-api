<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    private $entityManager;
    
    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager();
        
        parent::__construct($registry, Article::class);
    }

    public function getAllSortedQueryBuilder(array $orderBy = [])
    {
        $qb = $this->getQueryBuilder();
        
        foreach ($orderBy as $sort => $order) {
            $qb = $this->addOrderByQueryBuilder($qb, $sort, $order);
        }
        
        return $qb;
    }
    
    private function getQueryBuilder(QueryBuilder $qb = null)
    {
        return $qb ?: $this->createQueryBuilder('a');
    }
    
    private function addOrderByQueryBuilder(QueryBuilder $qb, string $sort,  string $order)
    {
        return $qb->addOrderBy("a.".$sort, $order);
    }
    
    public function save(Article $article)
    {
        $this->entityManager->persist($article);
        
        $this->entityManager->flush();
    }
    
    public function delete(Article $article)
    {
        $this->entityManager->remove($article);
    
        $this->entityManager->flush();
    }
    
    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
