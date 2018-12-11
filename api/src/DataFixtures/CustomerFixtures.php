<?php

namespace App\DataFixtures;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Customer;

class CustomerFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $customer = new Customer();
        $customer->setUsername('nook');
        $customer->setEmail('me@neilrichter.com');
        $customer->setPassword('$argon2i$v=19$m=1024,t=2,p=2$VHE1MVJNRVA5dko0SXlONw$Qz36my1Nyje+tggd4ufPXWmbaR/pjde796F8Oz3r2G0');
        $manager->persist($customer);
        $manager->flush();
    }
}
