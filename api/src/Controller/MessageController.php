<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Message;
use App\Entity\Ticket;
use App\Repository\MessageRepository;
use App\Repository\TicketRepository;

class MessageController extends AbstractController
{
    /**
     * @Route("/tickets/{identifier}/new-message", name="new-ticket-message")
     * @IsGranted("ROLE_USER")
     */
    public function newMessage($identifier, Request $request, TicketRepository $ticketRepository, ObjectManager $em)
    {
        $user = $this->getUser();
        $content = json_decode($request->getContent(), true)['message'];

        $message = new Message;
        $ticket = $ticketRepository->findOneBy(['identifier' => $identifier]);

        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $isParticipating = $ticket->getParticipants()->contains($user);

        if ($user != $ticket->getAuthor() && !$isAdmin && !$isParticipating) {
            throw new AccessDeniedHttpException;
        }
        
        $message
            ->setAuthor($user)
            ->setContent($content)
            ->setTicket($ticket);

        if ($isParticipating || $isAdmin) {
            $ticket->setStatus('awaiting');
        } else {
            $ticket->setStatus('open');
        }

        $em->persist($message);

        $em->flush();

        $message = [
            'id' => $message->getId(),
            'author' => $message->getAuthor()->getUsername(),
            'content' => $message->getContent(),
            'posted' => $message->getCreatedAt()->format('c'),
        ];

        return $this->json([
            'message' => $message,
            'ticket' => [
                'status' => $ticket->getStatus(),
            ],
        ], 201);
    }

    /**
     * @Route("/tickets/{identifier}/message/{id}", name="delete-ticket-message", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteMessage($identifier, $id, Request $request, MessageRepository $messageRepository, TicketRepository $ticketRepository, ObjectManager $em)
    {
        $user = $this->getUser();

        $ticket = $ticketRepository->findOneBy(['identifier' => $identifier]);

        $message = $messageRepository->findOneBy([
            'id' => $id,
            'ticket' => $ticket,
        ]);

        $id = $message->getId();

        $em->remove($message);
        $em->flush();

        return $this->json([
            'message' => ['id' => $id],
        ]);
    }

    /**
     * @Route("/tickets/{identifier}/message/{id}", name="ticket-message", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function getMessage($identifier, $id, Request $request, MessageRepository $messageRepository, TicketRepository $ticketRepository)
    {
        $user = $this->getUser();

        $ticket = $ticketRepository->findOneBy(['identifier' => $identifier]);

        $message = $messageRepository->findOneBy([
            'id' => $id,
            'ticket' => $ticket,
        ]);

        return $this->json([
            'message' => [
                'id' => $message->getId(),
                'author' => $message->getAuthor()->getUsername(),
                'content' => $message->getContent(),
                'posted' => $message->getCreatedAt()->format('c'),
            ],
            'ticket' => [
                'title' => $ticket->getTitle(),
            ],
        ]);
    }

    /**
     * @Route("/tickets/{identifier}/message/{id}", name="edit-ticket-message", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function editMessage($identifier, $id, Request $request, MessageRepository $messageRepository, TicketRepository $ticketRepository, ObjectManager $em)
    {
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);
        dump($data);

        $ticket = $ticketRepository->findOneBy(['identifier' => $identifier]);

        $message = $messageRepository->findOneBy([
            'id' => $id,
            'ticket' => $ticket,
        ]);

        $message->setContent($data['text']);
        $em->flush();

        return $this->json([
            'message' => [
                'id' => $message->getId(),
                'author' => $message->getAuthor()->getUsername(),
                'content' => $message->getContent(),
                'posted' => $message->getCreatedAt()->format('c'),
            ],
            'ticket' => [
                'title' => $ticket->getTitle(),
            ],
        ]);
    }
}
