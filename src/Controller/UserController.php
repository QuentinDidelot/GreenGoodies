<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    /**
     * Affiche la page d'inscription
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Créer un nouvel utilisateur
        $user = new User();

        // Créer le formulaire d'inscription
        $form = $this->createForm(RegistrationType::class, $user);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
    
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/registration.html.twig', [
            'registrationType' => $form->createView(),
        ]);
    }
}
