<?php


namespace App\Tests\Functional;


use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ArticleResourceTest extends WebTestCase
{
    const PATH = "/articles/";
    
    private $API_SECRET_KEY;
    
    public function setUp(): void
    {
        $this->API_SECRET_KEY = $_ENV['API_SECRET_KEY'];
    }
    
    public function testGetArticles()
    {
        $client = self::createClient();

        $client->request("GET", self::PATH);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testPostArticlesAnonimously()
    {
        $client = self::createClient();
    
        $client->request(
            "POST",
            self::PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                "title" => "Title",
                "body" => "Body",
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
    
    public function testPostArticlesAuthorized()
    {
        $client = self::createClient();
        
        $client->request(
            "POST",
            self::PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'ACCEPT' => 'application/json',
                'HTTP_Authorization' => "Bearer {$this->API_SECRET_KEY}"
            ],
            json_encode([
                "title" => "Title",
                "body" => "Body",
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }
    
    public function testGetArticle()
    {
        $client = self::createClient();

        $article = $this->createArticle();

        $client->request("GET", self::PATH . $article->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUpdateArticleAnonimously()
    {
        $client = self::createClient();

        $article = $this->createArticle();
    
        $client->request(
            "PUT",
            self::PATH . $article->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                "title" => "Updated title",
                "body" => "Updated body",
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateArticleAuthorized()
    {
        $client = self::createClient();

        $article = $this->createArticle();
    
        $client->request(
            "PUT",
            self::PATH . $article->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => "Bearer {$this->API_SECRET_KEY}"
            ],
            json_encode([
                "title" => "Updated title",
                "body" => "Updated body",
            ])
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals("Updated title", $data["title"]);
        $this->assertEquals("Updated body", $data["body"]);
    }
    
    public function testDeleteArticleAnonimously()
    {
        $client = self::createClient();
        
        $article = $this->createArticle();
        
        $client->request(
            "DELETE",
            self::PATH . $article->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
    
    public function testDeleteArticleAuthorized()
    {
        $client = self::createClient();
        
        $article = $this->createArticle();
        
        $client->request(
            "DELETE",
            self::PATH . $article->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => "Bearer {$this->API_SECRET_KEY}"
            ]
        );
        
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
    
    public function testBadRequest()
    {
        $client = self::createClient();

        $client->request(
            "POST",
            self::PATH,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => "Bearer {$this->API_SECRET_KEY}"
            ],
            json_encode([
                "title" => "",
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
    
    private function createArticle($title = "Title", $body = "Body"): Article
    {
        $article = new Article();
        $article->setTitle($title)
            ->setBody($body)
        ;

        $em = self::$container->get('doctrine')->getManager();

        $em->persist($article);
        $em->flush();

        return $article;
    }
}