<?php


namespace App\Service;


use App\Entity\Article;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArticleService
{
    const ORDER_BY = ["id" => "DESC"];
    
    private $articleRepository;
    
    private $paginator;
    
    private $validator;
    
    public function __construct(ArticleRepository $articleRepository, PaginatorInterface $paginator, ValidatorInterface $validator)
    {
        $this->articleRepository = $articleRepository;
        $this->paginator = $paginator;
        $this->validator = $validator;
    }
    
    public function paginate(?array $orderBy, ?int $page, ?int $limit)
    {
        $orderBy = $orderBy ?? self::ORDER_BY;
        $page = $page ?? 1;
        $limit = $limit ?? 10;
        
        $qbArticle = $this->articleRepository->getAllSortedQueryBuilder($orderBy);
        
        return $this->paginator->paginate(
            $qbArticle,
            $page,
            $limit
        );
    }
    
    public function save(Article $article)
    {
        $this->articleRepository->save($article);
        
        return $article;
    }
    
    public function getById(int $id)
    {
        return $this->articleRepository->findOneBy([
            'id' => $id,
        ]);
    }
    
    public function update(Article $article)
    {
        $this->articleRepository->save($article);
        
        return $article;
    }
    
    public function delete(Article $article)
    {
        $this->articleRepository->delete($article);
    }
}