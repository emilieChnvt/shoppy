<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_profile')]
    public function index(Profile $profile): Response
    {
        return $this->render('profile/index.html.twig', [
            'profile' => $profile,
        ]);
    }

                #[Route('/profile/edit/{id}', name: 'app_profile_edit')]
                public function edit(Request $request, EntityManagerInterface $manager): Response
                {

                    $profile = $this->getUser()->getProfile();
                    if($this->getUser()->getProfile() !== $profile) {
                        return $this->redirectToRoute('app_login');
                    }
                    if(!$profile){return $this->redirectToRoute('app_login');}

                    $profileForm = $this->createForm(ProfileForm::class, $profile);
                    $profileForm->handleRequest($request);
                    if($profileForm->isSubmitted() && $profileForm->isValid()) {
                        $profile->setOfUser($this->getUser());
                        $manager->persist($profile);
                        $manager->flush();
                        return $this->redirectToRoute('app_profile', ['id' => $profile->getId()]);

                    }

                    return $this->render('profile/edit.html.twig', [
                        'profileForm' => $profileForm->createView(),

                    ]);

                }
            }
