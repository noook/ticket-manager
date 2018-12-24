<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Ticket;

class TicketController extends AbstractController
{
    /**
     * @Route("/ticket", name="ticket")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        $manager = $this->getDoctrine()->getRepository(Ticket::class);
        $data = $manager->findAll();
        $data = $data[0];
        return $this->json([
            'title' => $data->getTitle(),
            'author' => $data->getAuthor()->getUsername(),
            'participants' => $data->getParticipants(),
        ]);
    }
}
