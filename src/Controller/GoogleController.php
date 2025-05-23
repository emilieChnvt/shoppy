<?php


namespace App\Controller;



use KnpU\OAuth2ClientBundle\Client\ClientRegistry;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


use Symfony\Component\HttpFoundation\Request;



use Symfony\Component\Routing\Annotation\Route;


use League\OAuth2\Client\Provider\Exception\IdentityProviderException;


use App\Entity\User;


use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


use Doctrine\ORM\EntityManagerInterface;


use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;


use App\Security\SecurityAuthenticator;


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


        SecurityAuthenticator $authenticator)


    {


        if ($this->getUser()) {


            return $this->redirectToRoute('app_products');


        }


        $client = $clientRegistry->getClient('google');


        try {


            $facebookUser = $client->fetchUser();


// check if email exist


            $existingUser = $entityManager->getRepository(User::class)


                ->findOneBy(['email' => $facebookUser->getEmail()]);


            if($existingUser){


                return $userAuthenticator->authenticateUser(


                    $existingUser,


                    $authenticator,


                    $request


                );


            }


            $user = new User();


            $user->setPassword(


                $userPasswordHasher->hashPassword(


                    $user,


                    $facebookUser->getId()


                )


            );


            $user->setEmail($facebookUser->getEmail());


            $entityManager->persist($user);


            $entityManager->flush();


            return $userAuthenticator->authenticateUser(


                $user,


                $authenticator,


                $request


            );


        } catch (IdentityProviderException $e) {


            var_dump($e->getMessage()); die;


        }


    }


}
