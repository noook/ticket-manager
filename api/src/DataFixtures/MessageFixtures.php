<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use App\DataFixtures\UserFixtures;
use App\DataFixtures\TicketFixtures;

use App\Repository\UserRepository;

use App\Entity\Message;
use App\Entity\Ticket;
use Doctrine\ORM\Query\ResultSetMapping;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $ticketManager = $manager->getRepository(Ticket::class);
        
        $now = new \DateTime();
        $ticket = $ticketManager->findAll()[0];
        $admin = $this->userRepository->findOneAdmin();
        $user = $this->userRepository->findOneNotAdmin();
        $messages = [];
        
        $originMessage = new Message;
        $originMessage->setTicket($ticket);
        $originMessage->setAuthor($user);
        $originMessage->setContent("Hello,\n Everytime I try to run a golang script it keeps telling me that my GOPATH isn't set, how can I solve this ?");
        $originMessage->setCreatedAt($now);
        $manager->persist($originMessage);

        $manager->flush(); // flush messages one by one otherwise all messages will have the same created at value

        $ticket->setParticipants([$admin]);

        $later = $now->add(new \DateInterval('PT1H'));
        $secondMessage = new Message;
        $secondMessage->setTicket($ticket);
        $secondMessage->setAuthor($admin);
        $secondMessage->setContent("Dear nook,\nHave you tried searching between your keyboard and your chair ? The problem might come from here.");
        $secondMessage->setCreatedAt($later);
        $manager->persist($secondMessage);
        
        $manager->flush();

        $later = $later->add(new \DateInterval('PT1H'));
        $thirdMessage = new Message;
        $thirdMessage->setTicket($ticket);
        $thirdMessage->setAuthor($user);
        $thirdMessage->setContent("Oh yes indeed, I didn't read the manual nor the docs, I am very dumb thanks for figuring out what the problem was.");
        $thirdMessage->setCreatedAt($later);
        $manager->persist($thirdMessage);

        $ticket->setStatus('closed');
        
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TicketFixtures::class,
        ];
    }
}
