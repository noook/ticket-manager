<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use App\DataFixtures\UserFixtures;

use App\Entity\Ticket;
use App\Entity\User;
use App\Service\TicketHandler;

class TicketFixtures extends Fixture implements DependentFixtureInterface
{
    private $ticketHandler;

    public function __construct(TicketHandler $ticketHandler)
    {
        $this->ticketHandler = $ticketHandler;
    }

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
            $ticket->setIdentifier($_ENV['TICKET_PREFIX'].'-'.$this->ticketHandler->generate());
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
