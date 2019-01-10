<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="users-list", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(UserRepository $userRepository)
    {
        $roles = ['ROLE_ADMIN'];
        $users = [];
        foreach ($userRepository->findAll() as $user) {
            $users[] = [
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
                'email' => $user->getEmail(),
                'id' => $user->getId(),
            ];
        }
        return $this->json([
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * @Route("/users/search", name="user-search", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function search(Request $request, UserRepository $userRepository)
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

    /**
     * @Route("/users/update/roles", name="user-update-roles", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function updateRoles(Request $request, UserRepository $userRepository, ObjectManager $em)
    {
        $data = json_decode($request->getContent(), true)['payload'];

        foreach ($data as $id => $roles) {
            $user = $userRepository->find($id);
            $user->setRoles($roles);
            $em->flush();
        }

        return $this->json([]);
    }
}
