<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class TokenHandler {
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function refreshToken($token)
    {
        $user = $this->em->getRepository(User::class)->findOneBy([
            'apiToken' => $token
        ]);
        if (is_null($user)) {
            return 401;
        }
        $newToken = $this->generate();
        $user->setApiToken($newToken);
        $now = new \DateTime();
        $user->setTokenExpiracy($now->add(new \DateInterval('P1D')));
        $this->em->flush();

        return $newToken;
    }

    public function generate(): String
    {
        $newToken = bin2hex(random_bytes(50));
        if (!$this->isAvailable($newToken)) {
            $this->generate();
        }
        return $newToken;
    }

    private function isAvailable($token): Bool
    {
        return !$this->em->getRepository(User::class)->findOneBy([
            'apiToken' => $token
        ]);
    }
}
