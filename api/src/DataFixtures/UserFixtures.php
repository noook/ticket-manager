<?php

namespace App\DataFixtures;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Service\TokenHandler;

class UserFixtures extends Fixture
{
    private $passwordEncoder;
    private $tokenHandler;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, TokenHandler $tokenHandler)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenHandler = $tokenHandler;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('nook');
        $user->setEmail('me@neilrichter.com');
        $user->setPassword('$argon2i$v=19$m=1024,t=2,p=2$VHE1MVJNRVA5dko0SXlONw$Qz36my1Nyje+tggd4ufPXWmbaR/pjde796F8Oz3r2G0');
        $user->setApiToken($this->tokenHandler->generate());
        $datetime = new \DateTime();
        $user->setTokenExpiracy($datetime->add(new \DateInterval('P1D')));
        $manager->persist($user);
        $manager->flush();
    }
}
