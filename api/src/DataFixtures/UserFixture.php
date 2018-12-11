<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('me@neilrichter.com');
        $user->setPassword('$argon2i$v=19$m=1024,t=2,p=2$LktYVUVVNjRyd2JDS0RwYw$LgnfNsKh7NtwNK0gW4OKIosoewmQd/RsPeujXvFIaEE');
        $manager->persist($user);
        $manager->flush();
    }
}
