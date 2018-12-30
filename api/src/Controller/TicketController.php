<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Security\TokenAuthenticator;
use App\Entity\Ticket;
use App\Repository\UserRepository;
use App\Repository\TicketRepository;
use App\Security\UserProvider;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Psr\Log\LoggerInterface;
use App\Service\TicketHandler;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Message;

class TicketController extends AbstractController
{
    /**
     * @Route("/tickets", name="tickets")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        $user = $this->getUser();
        $tickets = [];
        dump($user->getTickets());
        foreach ($user->getTickets() as $ticket) {
            $tickets[] = [
                'identifier' => $ticket->getIdentifier(),
                'title' => $ticket->getTitle(),
                'author' => $ticket->getAuthor()->getUsername(),
                'status' => $ticket->getStatus(),
                'created' => $ticket->getCreatedAt()->format('c'),
                'updated' => $ticket->getUpdatedAt()->format('c'),
            ];
        }
        return $this->json($tickets);
    }

    /**
     * @Route("/tickets/new", name="new-ticket")
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, TicketHandler $ticketHandler, ObjectManager $em)
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $ticket = new Ticket;
        $ticket
            ->setAuthor($user)
            ->setStatus('open')
            ->setParticipants([])
            ->setTitle($data['subject'])
            ->setIdentifier($ticketHandler->generate());

        $em->persist($ticket);

        $message = new Message;
        $message
            ->setAuthor($user)
            ->setContent($data['content'])
            ->setTicket($ticket);

        $em->persist($message);

        $em->flush();

        return $this->json([
            'identifier' => $ticket->getIdentifier(),
        ], 201);
    }

    /**
     * @Route("/tickets/{identifier}", name="ticket-detail")
     * @IsGranted("ROLE_USER")
     */
    public function detail($identifier, TicketRepository $ticketRepository, LoggerInterface $logger, UserRepository $userRepository)
    {
        $ticket = $ticketRepository->findOneBy(['identifier' => $identifier]);
        $user = $this->getUser();

        $isOwner = $ticket->getAuthor()->getId() == $user->getId();
        
        if (!$isOwner) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $ticketMessages = $ticket->getMessages();
        $messages = [];
        $participants = [];

        foreach ($ticket->getParticipants() as $participant) {
            $participants[] = $userRepository->findOneBy(['id' => $participant])->getUsername();
        }

        $ticket = [
            'identifier' => $ticket->getIdentifier(),
            'title' => $ticket->getTitle(),
            'author' => $ticket->getAuthor()->getUsername(),
            'status' => $ticket->getStatus(),
            'created' => $ticket->getCreatedAt()->format('c'),
            'updated' => $ticket->getUpdatedAt()->format('c'),
        ];

        foreach ($ticketMessages as $message) {
            $messages[] = [
                'author' => $message->getAuthor()->getUsername(),
                'content' => $message->getContent(),
                'posted' => $message->getCreatedAt()->format('c'),
            ];
        }

        return $this->json([
            'ticket' => $ticket,
            'messages' => $messages,
            'participants' => $participants,
        ]);
    }

    /**
     * @Route("/tickets/{identifier}/new-message", name="new-ticket-message")
     * @IsGranted("ROLE_USER")
     */
    public function newMessage($identifier, Request $request, TicketRepository $ticketRepository, ObjectManager $em)
    {
        $user = $this->getUser();
        $content = json_decode($request->getContent(), true)['message'];

        $message = new Message;
        $message
            ->setAuthor($user)
            ->setContent($content)
            ->setTicket($ticketRepository->findOneBy(['identifier' => $identifier]));

        $em->persist($message);

        $em->flush();

        dump($message);

        $message = [
            'author' => $message->getAuthor()->getUsername(),
            'content' => $message->getContent(),
            'posted' => $message->getCreatedAt()->format('c'),
        ];

        return $this->json([
            'message' => $message,
        ], 201);
    }
}
