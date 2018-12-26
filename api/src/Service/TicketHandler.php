<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Ticket;

class TicketHandler {
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function generate(): String
    {
        $newIdentifier = strtoupper(bin2hex(random_bytes(4)));
        if (!$this->isAvailable($newIdentifier)) {
            $this->generate();
        }
        return $newIdentifier;
    }

    private function isAvailable($identifer): Bool
    {
        return !$this->em->getRepository(Ticket::class)->findOneBy([
            'identifier' => $identifer
        ]);
    }
}
