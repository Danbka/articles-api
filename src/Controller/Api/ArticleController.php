<?php

namespace App\Controller\Api;

use App\Exception\ApiException;
use App\Service\ArticleService;
use App\Service\SerializerService;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Article;

/**
 * @Route("/articles")
 */
class ArticleController extends ApiController
{
    private $articleService;
    
    private $validator;
    
    private $serializer;
    
    public function __construct(ArticleService $articleService, ValidatorInterface $validator, SerializerService $serializer)
    {
        $this->articleService = $articleService;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }
    
    /**
     * Get all articles
     *
     * @Route("/", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns articles",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Article::class))
     *     )
     * )
     *
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     description="Page number"
     * )
     *
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="Limit"
     * )
     *
     * @SWG\Parameter(
     *     name="order[id]",
     *     in="query",
     *     type="string",
     *     enum={"ASC", "DESC"},
     * )
     *
     * @SWG\Parameter(
     *     name="order[title]",
     *     in="query",
     *     type="string",
     *     enum={"ASC", "DESC"},
     * )
     *
     * @SWG\Tag(name="Articles")
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $pagination = $this->articleService->paginate(
            $request->get('order'),
            $request->get('page'),
            $request->get('limit')
        );
        
        return $this->createApiResponse(
            $this->serializer->serialize([
                "items" => $pagination->getItems(),
                "totalItemCount" => $pagination->getTotalItemCount(),
                "currentPageNumber" => $pagination->getCurrentPageNumber(),
                "itemNumberPerPage" => $pagination->getItemNumberPerPage(),
            ], ['groups' => 'article:read'])
        );
    }
    
    /**
     * @Route("/", methods={"POST"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="Create article",
     *     @Model(type=Article::class)
     * )
     *
     * @SWG\Parameter(
     *     name="article",
     *     in="body",
     *     type="object",
     *     format="application/json",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="title", type="string"),
     *         @SWG\Property(property="body", type="string")
     *     )
     * )
     *
     * @SWG\Tag(name="Articles")
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        /** @var Article $article */
        $data = $this->serializer->deserialize(
            $request->getContent(),
            Article::class,
            ['groups' => 'article:write']
        );
        
        $violations = $this->validator->validate($data);
        if ($violations->count() > 0) {
            return $this->createApiResponse(
                $this->serializer->serialize($violations),
                Response::HTTP_BAD_REQUEST
            );
        }
        
        $article = $this->articleService->save($data);
        
        return $this->createApiResponse(
            $this->serializer->serialize($article),
            Response::HTTP_CREATED
        );
    }
    
    /**
     * @Route("/{id}", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Get article",
     *     @Model(type=Article::class)
     * )
     *
     * @SWG\Tag(name="Articles")
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        $article = $this->articleService->getById($id);
        if (is_null($article)) {
            throw new ApiException(Response::HTTP_NOT_FOUND, "Article not found");
        }
        
        return $this->createApiResponse(
            $this->serializer->serialize($article)
        );
    }
    
    /**
     * @Route("/{id}", methods={"PUT"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Update article",
     *     @Model(type=Article::class)
     * )
     *
     * @SWG\Parameter(
     *     name="article",
     *     in="body",
     *     type="object",
     *     format="application/json",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="title", type="string"),
     *         @SWG\Property(property="body", type="string")
     *     )
     * )
     *
     * @SWG\Tag(name="Articles")
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function update(int $id, Request $request)
    {
        $article = $this->articleService->getById($id);
        if (is_null($article)) {
            throw new ApiException(Response::HTTP_NOT_FOUND, "Article not found");
        }
    
        /** @var Article $data */
        $data = $this->serializer->deserialize(
            $request->getContent(),
            Article::class,
            [
                'groups' => 'article:write',
                AbstractNormalizer::OBJECT_TO_POPULATE => $article
            ]
        );
    
        $violations = $this->validator->validate($data);
        if ($violations->count() > 0) {
            return $this->createApiResponse(
                $this->serializer->serialize($violations),
                Response::HTTP_BAD_REQUEST
            );
        }
    
        $article = $this->articleService->update($data);
    
        return $this->createApiResponse(
            $this->serializer->serialize($article),
            Response::HTTP_OK
        );
    }
    
    /**
     * @Route("/{id}", methods={"DELETE"})
     *
     * @SWG\Response(
     *     response=204,
     *     description="Delete article",
     * )
     *
     * @SWG\Tag(name="Articles")
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id)
    {
        $article = $this->articleService->getById($id);
        if (is_null($article)) {
            throw new ApiException(Response::HTTP_NOT_FOUND, "Article not found");
        }
    
        $this->articleService->delete($article);
        
        return $this->createApiResponse(null, Response::HTTP_NO_CONTENT);
    }
}
