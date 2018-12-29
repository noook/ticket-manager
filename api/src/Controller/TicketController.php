<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Security\TokenAuthenticator;
use App\Entity\Ticket;
use App\Repository\TicketRepository;
use App\Security\UserProvider;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Psr\Log\LoggerInterface;

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
     * @Route("/tickets/{identifier}", name="ticket-detail")
     * @IsGranted("ROLE_USER")
     */
    public function detail($identifier, TicketRepository $ticketRepository, LoggerInterface $logger)
    {
        $ticket = $ticketRepository->findOneBy(['identifier' => $identifier]);
        $user = $this->getUser();

        $isOwner = $ticket->getAuthor()->getId() == $user->getId();
        
        if (!$isOwner) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $ticketMessages = $ticket->getMessages();
        $messages = [];

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
        ]);
    }
}
