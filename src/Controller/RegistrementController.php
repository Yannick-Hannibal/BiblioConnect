<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Form\RegisterformType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegistrementController extends AbstractController
{
    #[Route('/registrement', name: 'app_registrement')]
    public function index(UserPasswordHasherInterface $passwordHasher, Request $request, EntityManagerInterface $entityManager): Response
    {
        //créer une instance de user
        $user = new User();
        //Relier le formulaire registration à mon instance user
        $form = $this->createForm(RegisterformType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //Récuperer le password
            $plaintextPassword = $form->get("plainPassword")->getData();
            // hash the password (based on the security.yaml config for the $user class)
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setRoles(array('ROLE_USER'));
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Compte créé avec succès !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registrement/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
