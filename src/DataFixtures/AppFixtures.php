<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var Factory
     */
    private $faker;

    private const USERS = [
        [
            'username' => 'admin',
            'email' => 'admin@blog.com',
            'name' => 'admin test',
            'password' => 'Secret1234',
            'roles'    => [User::ROLE_ADMIN]
        ],
        [
            'username' => 'superadmin',
            'email' => 'superadmin@blog.com',
            'name' => 'superadmin test',
            'password' => 'Secret1234',
            'roles'    => [User::ROLE_SUPERADMIN]
        ],
        [
            'username' => 'enes',
            'email' => 'enes@blog.com',
            'name' => 'enes test',
            'password' => 'Secret1234',
            'roles'    => [User::ROLE_WRITER]
        ],
        [
            'username' => 'anida',
            'email' => 'anida@blog.com',
            'name' => 'anida test',
            'password' => 'Secret1234',
            'roles'    => [User::ROLE_WRITER]
        ],
        [
            'username' => 'milena',
            'email' => 'milen@blog.com',
            'name' => 'milena test',
            'password' => 'Secret1234',
            'roles'    => [User::ROLE_EDITOR]
        ],
        [
            'username' => 'drago',
            'email' => 'drago@blog.com',
            'name' => 'drago test',
            'password' => 'Secret1234',
            'roles'    => [User::ROLE_COMMENTATOR]
        ]
    ];

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            $blogPost = (new BlogPost())
                ->setTitle($this->faker->realText(30))
                ->setPublished($this->faker->dateTimeThisYear)
                ->setContent($this->faker->realText());

            $authorReference = $this->getRandomUserReference($blogPost);
            $blogPost->setAuthor($authorReference);
            $blogPost->setSlug($this->faker->slug);

            $this->setReference("blog_post_$i", $blogPost);

            $manager->persist($blogPost);
        }


        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < rand(1,10); $j++) {
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear);

                $authorReference = $this->getRandomUserReference($comment);

                $comment->setAuthor($authorReference);
                $comment->setBlogPost($this->getReference("blog_post_$i"));

                $manager->persist($comment);
            }
        }


        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userFixture) {
            $user = new User();
            $user->setUsername($userFixture['username']);
            $user->setEmail($userFixture['email']);
            $user->setName($userFixture['name']);

            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $userFixture['password']
            ));

            $user->setRoles($userFixture['roles']);
            $user->setEnabled(true);

            $this->addReference('user_' . $userFixture['username'], $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @return object
     */
    private function getRandomUserReference($entity)
    {
        $randomUser = self::USERS[rand(0,5)];

        if ($entity instanceof BlogPost && !count(array_intersect($randomUser['roles'], [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_WRITER]))) {

            return $this->getRandomUserReference($entity);
        }

        if ($entity instanceof Comment && !count(
            array_intersect(
                $randomUser['roles'],
                [
                    User::ROLE_SUPERADMIN,
                    User::ROLE_ADMIN,
                    User::ROLE_WRITER,
                    User::ROLE_COMMENTATOR
                ]
            )
            )) {

            return $this->getRandomUserReference($entity);
        }



        return $this->getReference('user_' . $randomUser['username']);
    }
}
