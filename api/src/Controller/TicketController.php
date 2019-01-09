<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @Route("/tickets", name="tickets", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(TicketRepository $ticketRepository)
    {
        $user = $this->getUser();

        $userTickets = $user->getTickets();

        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            $userTickets = $ticketRepository->findAll();
        }

        $tickets = [];
        foreach ($user->getParticipatingTo() as $ticket) {
            $latestMessage = array_reverse($ticket->getMessages()->toArray())[0];
            $tickets[] = [
                'identifier' => $ticket->getIdentifier(),
                'assigned' => true,
                'title' => $ticket->getTitle(),
                'author' => $ticket->getAuthor()->getUsername(),
                'status' => $ticket->getStatus(),
                'created' => $ticket->getCreatedAt()->format('c'),
                'updated' => $latestMessage->getCreatedAt()->format('c'),
            ];
        }

        foreach ($userTickets as $ticket) {
            if (array_search($ticket->getIdentifier(), array_column($tickets, 'identifier')) === false) {
                $latestMessage = array_reverse($ticket->getMessages()->toArray())[0];
                $tickets[] = [
                    'identifier' => $ticket->getIdentifier(),
                    'title' => $ticket->getTitle(),
                    'author' => $ticket->getAuthor()->getUsername(),
                    'status' => $ticket->getStatus(),
                    'created' => $ticket->getCreatedAt()->format('c'),
                    'updated' => $latestMessage->getCreatedAt()->format('c'),
                ];
            }
        }
          
        usort($tickets, function ($a, $b) {
            if ($a['created'] == $b['created']) {
                return 0;
            }
            
            return ($a['created'] > $b['created']) ? -1 : 1;
        });

        return $this->json($tickets);
    }

    /**
     * @Route("/tickets/{identifier}/status", name="ticket-status-update", methods={"PUT"})
     * @ParamConverter("ticket", class="App\Entity\Ticket", options={"mapping": {"identifier": "identifier"}})
     * @IsGranted("ROLE_ADMIN")
     */
    public function setStatus(Ticket $ticket, Request $request, ObjectManager $em)
    {
        $status = json_decode($request->getContent(), true)['status'];
        $ticket->setStatus($status);
        $em->flush();

        return $this->json([
            'newStatus' => $ticket->getStatus(),
        ]);
    }

    /**
     * @Route("/tickets/{identifier}/edit", name="ticket-title-update", methods={"PUT"})
     * @ParamConverter("ticket", class="App\Entity\Ticket", options={"mapping": {"identifier": "identifier"}})
     * @IsGranted("ROLE_ADMIN")
     */
    public function updateTitle(Ticket $ticket, Request $request, ObjectManager $em)
    {
        $content = json_decode($request->getContent(), true)['content'];
        $ticket->setTitle($content);
        $em->flush();

        return $this->json([
            'title' => $ticket->getTitle(),
        ]);
    }

    /**
     * @Route("/tickets/new", name="new-ticket", methods={"POST"})
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
     * @Route("/tickets/{identifier}/delete", name="delete-ticket", methods={"DELETE"})
     * @ParamConverter("ticket", class="App\Entity\Ticket", options={"mapping": {"identifier": "identifier"}})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Ticket $ticket, Request $request, ObjectManager $em)
    {
        $em->remove($ticket);
        $em->flush();

        return $this->json([], 205);
    }

    /**
     * @Route("/tickets/{identifier}", name="ticket-detail", methods={"GET"})
     * @ParamConverter("ticket", class="App\Entity\Ticket", options={"mapping": {"identifier": "identifier"}})
     * @IsGranted("ROLE_USER")
     */
    public function detail(Ticket $ticket, UserRepository $userRepository)
    {
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
                'id' => $message->getId(),
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
     * @Route("/tickets/{identifier}/add-participant", name="new-ticket-participant", methods={"POST"})
     * @ParamConverter("ticket", class="App\Entity\Ticket", options={"mapping": {"identifier": "identifier"}})
     * @IsGranted("ROLE_ADMIN")
     */
    public function newParticipant(Ticket $ticket, Request $request, UserRepository $userRepository, ObjectManager $em)
    {
        $user = $this->getUser();
        $participantID = json_decode($request->getContent(), true)['participant'];
        $participant = $userRepository->findOneBy(['id' => $participantID]);
        $ticket->addParticipant($participant);

        $em->flush();

        return $this->json([
            'participant' => $participant->getUsername(),
        ]);
    }
    
    /**
     * @Route("/tickets/{identifier}/remove-participant", name="remove-ticket-participant", methods={"DELETE"})
     * @ParamConverter("ticket", class="App\Entity\Ticket", options={"mapping": {"identifier": "identifier"}})
     * @IsGranted("ROLE_ADMIN")
     */
    public function removeParticipant(Ticket $ticket, Request $request, UserRepository $userRepository, ObjectManager $em)
    {
        $username = json_decode($request->getContent(), true)['participant'];
        $participant = $userRepository->findOneBy(['username' => $username]);
        $ticket->removeParticipant($participant);

        $em->flush();

        return $this->json([
            'participant' => $participant->getUsername(),
        ]);
    }
}
