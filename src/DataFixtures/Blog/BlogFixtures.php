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
        for ($i = 0; $i < $countAuthors; $i++) {
            $this->loadAuthors($manager);
        }
    }
    private function loadAuthors(ObjectManager $manager)
    {
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
        $countPosts = random_int(3,7);
        for ($j = 0; $j < $countPosts; $j++) {
            $blogPost = new BlogPost();
            $title = $this->faker->unique()->sentence;
            $blogPost->setTitle($title)
                     //->setSlug($this->faker->slug)
                     ->setDescription($this->faker->text(15))
                     ->setBody($this->faker->text(100))
                     ->setAuthor($author);
            $manager->persist($blogPost);
        }
        $manager->flush();
    }
}