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
        $ticket = $ticketManager->findOneBy(['title' => 'Cannot set GOPATH']);
        $admin = $this->userRepository->findOneAdmin();
        $user = $this->userRepository->findOneNotAdmin();
        $messages = [];
        
        $originMessage = new Message;
        $originMessage
            ->setTicket($ticket)
            ->setAuthor($user)
            ->setContent("Hello,\n Everytime I try to run a golang script it keeps telling me that my GOPATH isn't set, how can I solve this ?")
            ->setCreatedAt($now);
        $manager->persist($originMessage);

        $manager->flush(); // flush messages one by one otherwise all messages will have the same created at value

        $ticket->addParticipant($admin);

        $later = $now->add(new \DateInterval('PT1H'));
        $secondMessage = new Message;
        $secondMessage
            ->setTicket($ticket)
            ->setAuthor($admin)
            ->setContent("Dear nook,\nHave you tried searching between your keyboard and your chair ? The problem might come from here.")
            ->setCreatedAt($later);
        $manager->persist($secondMessage);
        
        $manager->flush();

        $later = $later->add(new \DateInterval('PT1H'));
        $thirdMessage = new Message;
        $thirdMessage
            ->setTicket($ticket)
            ->setAuthor($user)
            ->setContent("Oh yes indeed, I didn't read the manual nor the docs, I am very dumb thanks for figuring out what the problem was.")
            ->setCreatedAt($later);
        $manager->persist($thirdMessage);

        $ticket->setStatus('closed');
        
        $manager->flush();

        $ticket = $ticketManager->findOneBy(['title' => "Server crashed I don't know why"]);
        $message = new Message;
        $message
            ->setAuthor($user)
            ->setContent("Hello,\n I tried to run an infinite loop on my server but I don't understand the server does not respond anymore what am I doing wrong?")
            ->setTicket($ticket);
        $manager->persist($message);
        $manager->flush();

        $ticket = $ticketManager->findOneBy(['title' => 'Oops, lost my password']);
        $now = new \DateTime();
        $message = new Message;
        $message
            ->setAuthor($user)
            ->setContent('I know my password is "password" but when I type "12345678" it tells me my password is incorrect, why ?')
            ->setTicket($ticket);
        $manager->persist($message);
        $manager->flush();

        $later = $now->add(new \DateInterval('PT1H'));
        $message = new Message;
        $message
            ->setAuthor($admin)
            ->setContent('Have you tried with the correct password ?')
            ->setTicket($ticket)
            ->setCreatedAt($later);
        $manager->persist($message);
        $manager->flush();

        $later = $later->add(new \DateInterval('PT1H'));
        $message = new Message;
        $message
            ->setAuthor($user)
            ->setContent('No why, should I ?')
            ->setTicket($ticket)
            ->setCreatedAt($later);
        $manager->persist($message);
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
