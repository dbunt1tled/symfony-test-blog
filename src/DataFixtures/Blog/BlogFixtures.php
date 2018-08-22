<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 18.07.18
 * Time: 19:37
 */

namespace App\DataFixtures\Blog;

use App\Entity\User;
use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\Image;
use App\Utils\FileUploader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BlogFixtures extends Fixture
{
    private $passwordEncoder;
    private $faker;
    /**
     * @var FileUploader
     */
    private $uploader;

    public function __construct(UserPasswordEncoderInterface $encoder, FileUploader $uploader)
    {
        $this->passwordEncoder = $encoder;
        $this->faker = Factory::create();
        $this->uploader = $uploader;
    }

    public function load(ObjectManager $manager)
    {
        $countUsers = random_int(10,20);
        $countCategories = random_int(1,6);
        $this->loaUsers($manager,$countUsers);
        $manager->flush();
        $this->loadCategories($manager,$countCategories);
        $manager->flush();
        $this->loadBlogPosts($manager);
        $manager->flush();
    }
    private function loaUsers(ObjectManager $manager, int $countUsers)
    {
        for ($i = 0; $i < $countUsers; $i++) {
            $user = new User();
            $num = random_int(0,20);
            $fileOrig = __DIR__.'/images/user/' .$num. '.png';
            $fileDest = __DIR__.'/images/user/tmp/' .$i. '.png';
            if(is_file($fileDest)){
                unlink($fileDest);
            }
            @copy($fileOrig, $fileDest);
            $file = new UploadedFile($fileDest, 'Image1', null, null, null, true);
            $nameUser = $this->faker->name;
            $fileName = $this->uploader->upload($file,$nameUser,$user->getTargetDirectory());

            $user->setName($nameUser)
                   ->setJob($this->faker->jobTitle)
                   ->setEmail($this->faker->unique()->safeEmail)
                   ->setPhone($this->faker->unique()->phoneNumber)
                   ->setShortBio($this->faker->text())
                   ->setImage($fileName)
                   ->setGithub($this->faker->word)
                   ->setTwitter($this->faker->word)
                   ->setPlainPassword('12345678')
                   ->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()))
                   ->setRole([User::ROLE_USER]);
            $manager->persist($user);
        }
    }
    private function loadCategories(ObjectManager $manager, int $countCategories)
    {
        $readyCategories = [];
        for ($i = 0; $i < $countCategories; $i++) {
            $category = new Category();
            $num = random_int(0,5);
            $fileOrig = __DIR__.'/images/category/' .$num. '.jpg';
            $fileDest = __DIR__.'/images/category/tmp/' .$num. '.jpg';
            if(is_file($fileDest)){
                unlink($fileDest);
            }
            @copy($fileOrig, $fileDest);
            $file = new UploadedFile($fileDest, 'Image1', null, null, null, true);
            $nameCategory = $this->faker->name;
            $fileName = $this->uploader->upload($file,$nameCategory,$category->getTargetDirectory());
            $category->setName($nameCategory)
                   ->setDescription($this->faker->text(600))
                   ->setImage($fileName)
                   ->setStatus(Category::STATUS_ACTIVE);
            $manager->persist($category);
            $readyCategories[] = $category;
        }
        $readyCategories1 = [];
        foreach ($readyCategories as $readyCategory) {
            for ($i = 0; $i < $countCategories; $i++) {
                $category = new Category();
                $num = random_int(0,5);
                $fileOrig = __DIR__.'/images/category/' .$num. '.jpg';
                $fileDest = __DIR__.'/images/category/tmp/' .$num. '.jpg';
                if(is_file($fileDest)){
                    unlink($fileDest);
                }
                @copy($fileOrig, $fileDest);
                $file = new UploadedFile($fileDest, 'Image1', null, null, null, true);
                $nameCategory = $this->faker->name;
                $fileName = $this->uploader->upload($file,$nameCategory,$category->getTargetDirectory());
                $category->setName($nameCategory)
                         ->setDescription($this->faker->text(600))
                         ->setImage($fileName)
                         ->setStatus(Category::STATUS_ACTIVE)
                         ->setParent($readyCategory);
                $manager->persist($category);
                $readyCategories1[] = $category;
            }
        }
        foreach ($readyCategories1 as $readyCategory) {
            for ($i = 0; $i < $countCategories; $i++) {
                $category = new Category();
                $num = random_int(0,5);
                $fileOrig = __DIR__.'/images/category/' .$num. '.jpg';
                $fileDest = __DIR__.'/images/category/tmp/' .$num. '.jpg';
                if(is_file($fileDest)){
                    unlink($fileDest);
                }
                @copy($fileOrig, $fileDest);
                $file = new UploadedFile($fileDest, 'Image1', null, null, null, true);
                $nameCategory = $this->faker->name;
                $fileName = $this->uploader->upload($file,$nameCategory,$category->getTargetDirectory());
                $category->setName($nameCategory)
                         ->setDescription($this->faker->text(600))
                         ->setImage($fileName)
                         ->setStatus(Category::STATUS_ACTIVE)
                         ->setParent($readyCategory);
                $manager->persist($category);
            }
        }
    }
    private function loadBlogPosts(ObjectManager $manager)
    {

        $userRepository = $manager->getRepository('App:User');
        $categoryRepository = $manager->getRepository('App:Category');
        $usersIds = $userRepository->getAllIds();
        $categoriesIds = $categoryRepository->getAllIds();
        for ($i = 0; $i < 30; $i++) {
            $userId = array_rand($usersIds, 1);
            $categoryId = array_rand($categoriesIds, 1);
            $user     = $userRepository->findOneBy(['id' => $usersIds[$userId]]);
            $category   = $categoryRepository->findOneBy(['id' => $categoriesIds[$categoryId]]);
            $countPosts = random_int(3,7);
            for ($j = 0; $j < $countPosts; $j++) {
                sleep(1);
                if (!$user || !$category) {
                    continue;
                }
                $blogPost = new BlogPost();
                $name    = $this->faker->unique()->sentence;
                $image = new Image();
                $image->setName($name);
                $image->setDescription($this->faker->text(300));
                $num = random_int(0,20);
                $fileOrig = __DIR__.'/images/post/' .$num. '.jpg';
                $fileDest = __DIR__.'/images/post/tmp/' .$i.'-'.$j. '.jpg';

                if(!copy($fileOrig, $fileDest)){
                    echo "не удалось скопировать $fileOrig...\n";
                    die();
                }
                $file = new UploadedFile($fileDest, 'Image'.$i.'-'.$j, null, null, null, true);
                $image->setFile($file);
                $manager->persist($image);

                $blogPost->setName($name)
                         ->setDescription($this->faker->text(600))
                         ->setBody($this->faker->text(2600))
                         ->setCategory($category)
                         ->setStatus(BlogPost::STATUS_ACTIVE)
                         ->setUser($user)
                         ->addImage($image);
                $manager->persist($blogPost);
            }
        }
    }
}