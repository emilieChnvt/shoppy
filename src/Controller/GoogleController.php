<?php

// src/Controller/GoogleController.php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Profile;
use App\Security\SecurityAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google_start')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_products');
        }

        return $clientRegistry
            ->getClient('google')
            ->redirect([], [
                'prompt' => 'select_account',
                'scope' => ['email', 'profile'],
            ]);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(
        Request $request,
        ClientRegistry $clientRegistry,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        SecurityAuthenticator $authenticator
    ) {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_products');
        }

        $client = $clientRegistry->getClient('google');

        try {
            $googleUser = $client->fetchUser();

            $existingUser = $entityManager->getRepository(User::class)
                ->findOneBy(['email' => $googleUser->getEmail()]);

            if ($existingUser) {
                return $userAuthenticator->authenticateUser(
                    $existingUser,
                    $authenticator,
                    $request
                );
            }

            $user = new User();
            $user->setEmail($googleUser->getEmail());
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $googleUser->getId())
            );

            $entityManager->persist($user);

            $profile = new Profile();
            $profile->setOfUser($user);

            $entityManager->persist($profile);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        } catch (IdentityProviderException $e) {
            dump($e->getMessage());
            die;
        }
    }
}
