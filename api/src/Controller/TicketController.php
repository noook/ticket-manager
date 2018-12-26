<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Security\TokenAuthenticator;
use App\Entity\Ticket;
use App\Security\UserProvider;

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
}
