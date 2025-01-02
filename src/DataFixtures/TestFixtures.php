<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use App\Entity\User;
use App\Entity\Forum;
use App\Entity\Journey;
use App\Entity\Carousel;
use App\Entity\Category;
use App\Entity\BlogComment;
use App\Entity\ForumComment;
use Faker\Factory as FakerFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture implements FixtureGroupInterface
{
    private $faker;
    private $hasher;
    private $manager;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->hasher = $hasher;
    }

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->loadCategories();
        $this->loadUsers();
        $this->loadForums();
        $this->loadForumComments();
        $this->loadBlogs();
        $this->loadBlogComments();
        $this->loadJourneys();
        $this->loadCarousel();
    }

    public function loadUsers(): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $user = new User();
            $user->setEmail($this->faker->email());
            $password = $this->hasher->hashPassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);
            $user->setUserName($this->faker->name());

            $this->manager->persist($user);
        }
        $this->manager->flush();
    }

    public function loadCategories(): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $category = new Category();
            $category->setTitle($this->faker->word());
            $this->manager->persist($category);
        }
        $this->manager->flush();
    }

    public function loadForums(): void
    {
        $categoryRepository = $this->manager->getRepository(Category::class);
        $userRepository = $this->manager->getRepository(User::class);

        $categories = $categoryRepository->findAll();
        $users = $userRepository->findAll();

        for ($i = 0; $i < 15; ++$i) {
            $forum = new Forum();
            $forum->setTitle($this->faker->word());
            $forum->setDescription($this->faker->sentence());
            $category = $this->faker->randomElement($categories);
            $forum->setCategory($category);
            $user = $this->faker->randomElement($users);
            $forum->setUser($user);
            $this->manager->persist($forum);
        }

        $this->manager->flush();
    }

    public function loadForumComments(): void
    {
        $forumRepository = $this->manager->getRepository(Forum::class);
        $userRepository = $this->manager->getRepository(User::class);

        $forums = $forumRepository->findAll();
        $users = $userRepository->findAll();

        for ($i = 0; $i < 25; ++$i) {
            $forumComment = new ForumComment();
            $forumComment->setDescription($this->faker->sentence());
            $forumComment->setForum($this->faker->randomElement($forums));
            $forumComment->setUser($this->faker->randomElement($users));
            $this->manager->persist($forumComment);
        }

        $this->manager->flush();
    }

    public function loadBlogs(): void
    {
        $userRepository = $this->manager->getRepository(User::class);

        $users = $userRepository->findAll();

        for ($i = 0; $i < 15; ++$i) {
            $blog = new Blog();
            $blog->setTitle($this->faker->word());
            $blog->setContent($this->faker->sentence());
            $user = $this->faker->randomElement($users);
            $blog->setUser($user);
            $this->manager->persist($blog);
        }

        $this->manager->flush();
    }

    public function loadBlogComments(): void
    {
        $blogRepository = $this->manager->getRepository(Blog::class);
        $userRepository = $this->manager->getRepository(User::class);

        $blogs = $blogRepository->findAll();
        $users = $userRepository->findAll();

        for ($i = 0; $i < 25; ++$i) {
            $blogComment = new BlogComment();
            $blogComment->setDescription($this->faker->sentence());
            $blogComment->setBlog($this->faker->randomElement($blogs));
            $blogComment->setUser($this->faker->randomElement($users));
            $this->manager->persist($blogComment);
        }

        $this->manager->flush();
    }

    public function loadJourneys(): void
    {
        $datas = [
            [
                'stage' => 'Calais - St Omer',
                'description' => 'Trajet de Calais vers St Omer',
                'gpxName' => 'Calais-St-Omer.gpx',
            ],
            [
                'stage' => 'St Omer - Bethune',
                'description' => 'Trajet de St Omer vers Bethune',
                'gpxName' => 'St-Omer-Bethune.gpx',

            ],
            [
                'stage' => 'Bethune- Lens',
                'description' => 'Trajet de Bethune vers Lens',
                'gpxName' => 'Bethune-Lens.gpx',
            ],
            [
                'stage' => 'Lens- Lille',
                'description' => 'Trajet de Lens vers Lille',
                'gpxName' => 'Lens-Lille.gpx',
            ],
            [
                'stage' => 'Lille - Wattrelos',
                'description' => 'Trajet de Lille vers Wattrelos',
                'gpxName' => 'Lille-Wattrelos.gpx',
            ]
        ];

       foreach ($datas as $data) {
            $journey = new Journey();
            $journey->setStage($data['stage']);
            $journey->setDescription($data['description']);
            $journey->setGpxName($data['gpxName']);
            $this->manager->persist($journey);
        }

        $this->manager->flush();
    }

    public function loadCarousel(): void
    {
        $datas = [
            [
                'imageName' => 'carte.png',
            ],
            [
                'imageName' => 'coucher_soleil.avif',
            ],
            [
                'imageName' => 'velo_ville.avif',
            ],
            [
                'imageName' => 'velos_colines.webp',
            ],
        ];

        foreach ($datas as $data) {
            $carousel = new Carousel();
            $carousel->setImageName($data['imageName']);
            $this->manager->persist($carousel);
        }

        $this->manager->flush();
    }
}
