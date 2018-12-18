<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);    
        $user->setPassword($passwordEncoder->encodePassword(
            $user,
            $data['password']
        ));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SecurityController.php',
            'user' => $user->repr(),
        ]);
    }
}
