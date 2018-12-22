<?php

namespace App\DataFixtures;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('nook');
        $user->setEmail('me@neilrichter.com');
        $user->setPassword('$argon2i$v=19$m=1024,t=2,p=2$VHE1MVJNRVA5dko0SXlONw$Qz36my1Nyje+tggd4ufPXWmbaR/pjde796F8Oz3r2G0');
        $user->setApiToken(bin2hex(random_bytes(50)));
        $manager->persist($user);
        $manager->flush();
    }
}
