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
    public function index(TicketRepository $ticketRepository)
    {
        $user = $this->getUser();
        $tickets = [];

        $userTickets = $user->getTickets();

        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            $userTickets = $ticketRepository->findAll();
        }

        foreach ($userTickets as $ticket) {
            $tickets[] = [
                'identifier' => $ticket->getIdentifier(),
                'title' => $ticket->getTitle(),
                'author' => $ticket->getAuthor()->getUsername(),
                'status' => $ticket->getStatus(),
                'created' => $ticket->getCreatedAt()->format('c'),
                'updated' => $ticket->getUpdatedAt()->format('c'),
            ];
        }

        foreach ($user->getParticipatingTo() as $ticket) {
            $tickets[] = [
                'identifier' => $ticket->getIdentifier(),
                'title' => $ticket->getTitle(),
                'author' => $ticket->getAuthor()->getUsername(),
                'status' => $ticket->getStatus(),
                'created' => $ticket->getCreatedAt()->format('c'),
                'updated' => $ticket->getUpdatedAt()->format('c'),
            ];
        }
        $tickets = array_unique($tickets, SORT_REGULAR);
          
        usort($tickets, function ($a, $b) {
            if ($a['created'] == $b['created']) {
                return 0;
            }
            
            return ($a['created'] > $b['created']) ? -1 : 1;
        });

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
        $isParticipating = $user->getParticipatingTo()->contains($ticket);
        
        if (!$isOwner && !$isParticipating) {
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

        $message = [
            'author' => $message->getAuthor()->getUsername(),
            'content' => $message->getContent(),
            'posted' => $message->getCreatedAt()->format('c'),
        ];

        return $this->json([
            'message' => $message,
        ], 201);
    }

    /**
     * @Route("/tickets/{identifier}/add-participant", name="new-ticket-participant")
     * @IsGranted("ROLE_ADMIN")
     */
    public function newParticipant($identifier, Request $request, UserRepository $userRepository, TicketRepository $ticketRepository, ObjectManager $em)
    {
        $user = $this->getUser();
        $participantID = json_decode($request->getContent(), true)['participant'];
        $ticket = $ticketRepository->findOneBy(['identifier' => $identifier]);
        $participant = $userRepository->findOneBy(['id' => $participantID]);
        $ticket->addParticipant($participant);

        $em->flush();

        return $this->json([
            'participant' => $participant->getUsername(),
        ]);
    }
    
    /**
     * @Route("/tickets/{identifier}/remove-participant", name="remove-ticket-participant")
     * @IsGranted("ROLE_ADMIN")
     */
    public function removeParticipant($identifier, Request $request, UserRepository $userRepository, TicketRepository $ticketRepository, ObjectManager $em)
    {
        $username = json_decode($request->getContent(), true)['participant'];
        $ticket = $ticketRepository->findOneBy(['identifier' => $identifier]);
        $participant = $userRepository->findOneBy(['username' => $username]);
        $ticket->removeParticipant($participant);

        $em->flush();

        return $this->json([
            'participant' => $participant->getUsername(),
        ]);
    }
}
