<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Security\TokenAuthenticator;
use Symfony\Component\Security\Core\Security;   
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Security\UserProvider;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);
        $errors = [];
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        
        $checkUsername = $userRepository->findOneBy([
            'username' => $data['username'],
        ]);
        if (!is_null($checkUsername)) {
            $errors['username'] = true;
        }

        $checkEmail = $userRepository->findOneBy([
            'email' => $data['email'],
        ]);
        if (!is_null($checkEmail)) {
            $errors['email'] = true;
        }

        if ($data['password'] != $data['passwordConfirmation']) {
            $errors['password'] = true;
        }

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors,
            ], 400);
        }

        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);    
        $user->setPassword($passwordEncoder->encodePassword(
            $user,
            $data['password']
        ));
        $user->setApiToken(bin2hex(random_bytes(50)));
        $now = new \DateTime();
        $user->setTokenExpiracy($now->add(new \DateInterval('P1D')));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'user' => $user->repr(),
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, UserProvider $userProvider,  UserPasswordEncoderInterface $passwordEncoder)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'username' => $data['username'],
        ]);


        if (is_null($user)) {
            return $this->json([
                'message' => 'Invalid username or password',
            ], 400);
        }

        if ($passwordEncoder->isPasswordValid($user, $data['password'])) {
            return $this->json([
                'token' => $user->getApiToken(),
                'expiracy' => $user->getTokenExpiracy(),
            ]);
        }

        return $this->json([
            'message' => 'Invalid username or password',
        ], 400);
        
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(Request $request, UserProvider $userProvider, TokenAuthenticator $authenticator)
    {
        $credentials = $authenticator->getCredentials($request);
        $user = $authenticator->getUser($credentials, $userProvider);

        return $this->json([
            'user' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ]);
    }
    /**
     * @Route("/user/check-connection", name="loggedInAs")
     */
    public function loggedInAs(Request $request, UserProvider $userProvider, TokenAuthenticator $authenticator)
    {
        $credentials = $authenticator->getCredentials($request);
        $user = $authenticator->getUser($credentials, $userProvider);
        if (is_null($user)) {
            throw new AccessDeniedHttpException('Invalid token');
        }

        return $this->json([
            'username' => $user->getUsername(),
        ]);
    }
}
