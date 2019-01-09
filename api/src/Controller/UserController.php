<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    /**
     * @Route("/users/search", name="user-search", methods={"GET"})
     */
    public function index(Request $request, UserRepository $userRepository)
    {
        $qs = explode('&', $request->getQueryString());
        $data = [];
        foreach ($qs as $field) {
            $key = explode('=', $field)[0];
            $value = explode('=', $field)[1];
            $data[$key] = $value;
        }
        $users = [];

        foreach ($userRepository->findOneLike($data['query']) as $user) {
            $users[] = [
                'username' => $user->getUsername(),
                'id' => $user->getId(),
            ];
        }
        return $this->json([
            'users' => $users,
        ]);
    }
}
