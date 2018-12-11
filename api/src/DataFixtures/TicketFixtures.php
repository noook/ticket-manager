<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use App\DataFixtures\UserFixtures;

use App\Entity\Ticket;
use App\Entity\User;

class TicketFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $em = $manager->getRepository(User::class);
        $customer = $em->findOneBy(['username' => 'nook']);
        
        $titles = [
            "Server crashed I don't know why",
            "Oops, lost my password",
            "Cannot set GOPATH",
        ];

        for ($i = 0; $i < count($titles); $i++) {
            $ticket = new Ticket();
            $ticket->setTitle($titles[$i]);
            $ticket->setAuthor($customer);
            $ticket->setStatus("open");
            $ticket->setParticipants([]);
            $manager->persist($ticket);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
