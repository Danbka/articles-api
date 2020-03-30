<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture
{
    private $faker;
    
    public function __construct()
    {
        $this->faker = Factory::create();
    }
    
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            $article = new Article();
            $article->setTitle($this->faker->title);
            $article->setBody($this->faker->text);
            
            $manager->persist($article);
        }

        $manager->flush();
    }
}
