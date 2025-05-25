<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Profile;
use App\Form\AddressForm;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AddressController extends AbstractController
{
    #[Route('/address', name: 'app_address')]
    public function index(AddressRepository $addressRepository): Response
    {
        return $this->render('address/index.html.twig', [
            'addresses' => $addressRepository->findAll(),
        ]);
    }

    #[Route('/address/{id}/new', name: 'app_address_new')]
    public function create(EntityManagerInterface $entityManager, Request $request, Profile $profile): Response
    {
       if($this->getUser()->getProfile() !== $profile) {
           return $this->redirectToRoute('app_products');
       }

       $address = new Address();
       $adresseForm = $this->createForm(AddressForm::class , $address);
       $adresseForm->handleRequest($request);

       if($adresseForm->isSubmitted() && $adresseForm->isValid()) {
           $address->setProfile($profile);
           $entityManager->persist($address);
           $entityManager->flush();
           return $this->redirectToRoute('app_profile', ['id' => $profile->getId()]);
       }
       return $this->render('address/new.html.twig', [
           'profile' => $profile,
           'addresseForm' => $adresseForm
       ]);
    }

    #[Route('/address/{id}/delete', name: 'app_address_delete')]
    public function delete(Address $address, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser()->getProfile() !== $address->getProfile()) {
            return $this->redirectToRoute('app_products');
        }
        $entityManager->remove($address);
        $entityManager->flush();
        return $this->redirectToRoute('app_profile', ['id' => $address->getProfile()->getId()]);
    }
}
