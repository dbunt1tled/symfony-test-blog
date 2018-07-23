<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 18.07.18
 * Time: 19:37
 */

namespace App\DataFixtures\Blog;

use App\Entity\Author;
use App\Entity\BlogPost;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BlogFixtures extends Fixture
{
    private $passwordEncoder;
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->passwordEncoder = $encoder;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $countAuthors = random_int(10,20);
        $countCategories = random_int(1,4);
        $this->loadAuthors($manager,$countAuthors);
        $manager->flush();
        $this->loadCategories($manager,$countCategories);
        $manager->flush();
        $this->loadBlogPosts($manager);
        $manager->flush();
    }
    private function loadAuthors(ObjectManager $manager, int $countAuthors)
    {
        for ($i = 0; $i < $countAuthors; $i++) {
            $author = new Author();
            $author->setName($this->faker->name)
                   ->setTitle($this->faker->jobTitle)
                   ->setEmail($this->faker->unique()->safeEmail)
                   ->setPhone($this->faker->unique()->phoneNumber)
                   ->setShortBio($this->faker->text(15))
                   ->setGithub($this->faker->word)
                   ->setTwitter($this->faker->word)
                   ->setPlainPassword('12345678')
                   ->setPassword($this->passwordEncoder->encodePassword($author, $author->getPlainPassword()))
                   ->setRole([Author::ROLE_USER]);
            $manager->persist($author);
        }
    }
    private function loadCategories(ObjectManager $manager, int $countCategories)
    {
        $readyCategories = [];
        for ($i = 0; $i < $countCategories; $i++) {
            $category = new Category();
            $category->setName($this->faker->name)
                   ->setDescription($this->faker->text(150))
                   ->setStatus(Category::STATUS_ACTIVE);
            $manager->persist($category);
            $readyCategories[] = $category;
        }
        $readyCategories1 = [];
        foreach ($readyCategories as $readyCategory) {
            for ($i = 0; $i < $countCategories; $i++) {
                $category = new Category();
                $category->setName($this->faker->name)
                         ->setDescription($this->faker->text(150))
                         ->setStatus(Category::STATUS_ACTIVE)
                         ->setParent($readyCategory);
                $manager->persist($category);
                $readyCategories1[] = $category;
            }
        }
        foreach ($readyCategories1 as $readyCategory) {
            for ($i = 0; $i < $countCategories; $i++) {
                $category = new Category();
                $category->setName($this->faker->name)
                         ->setDescription($this->faker->text(150))
                         ->setStatus(Category::STATUS_ACTIVE)
                         ->setParent($readyCategory);
                $manager->persist($category);
            }
        }
    }
    private function loadBlogPosts(ObjectManager $manager)
    {

        $authorRepository = $manager->getRepository('App:Author');
        $categoryRepository = $manager->getRepository('App:Category');
        $authorsIds = $authorRepository->getAllIds();
        $categoriesIds = $categoryRepository->getAllIds();
        for ($i = 0; $i < 30; $i++) {
            $authorId = array_rand($authorsIds, 1);
            $categoryId = array_rand($categoriesIds, 1);
            $author     = $authorRepository->findOneBy(['id' => $authorsIds[$authorId]]);
            $category   = $categoryRepository->findOneBy(['id' => $categoriesIds[$categoryId]]);
            $countPosts = random_int(3,7);
            for ($j = 0; $j < $countPosts; $j++) {
                if (!$author || !$category) {
                    continue;
                }
                $blogPost = new BlogPost();
                $title    = $this->faker->unique()->sentence;
                $blogPost->setTitle($title)
                         ->setDescription($this->faker->text(15))
                         ->setBody($this->faker->text(100))
                         ->setCategory($category)
                         ->setAuthor($author);
                $manager->persist($blogPost);
            }
        }
    }
}