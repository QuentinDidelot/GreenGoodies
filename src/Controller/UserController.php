<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * Affiche la page d'inscription 
     */
    #[Route('/registration', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

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

    /**
     * Affiche la page de connexion pour que l'utilisateur puisse se connecter
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $email = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'email' => $email,
            'error' => $error,
        ]);
    }
    

    /**
     * Déconnecte l'utilisateur de l'application
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        
    }
    

    /**
     * Affiche la page "Mon compte" de l'utilisateur enregistré
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/account', name: 'app_account')]
    public function account(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \Exception('User not found');
        }
        
        $orders = $user->getOrders(); 

        return $this->render('user/account.html.twig', [
            'user' => $user,
            'orders' => $orders, 
        ]);
    }

    /**
     * Supprime le compte utilisateur
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/account/delete', name: 'app_account_delete')]
    public function deleteAccount(EntityManagerInterface $entityManager): Response {
        $user = $this->getUser();
    
        if (!$user instanceof User) {
            throw new \Exception('User not found');
        }
    
        // Supprime le compte utilisateur et les données associées
        try {
            $entityManager->remove($user);
            $entityManager->flush();
        } catch (\Exception $e) {
            return $this->redirectToRoute('app_account', [
                'error' => 'Une erreur est survenue lors de la suppression de votre compte. Réessayez ultérieurement.'
            ]);
        }
    
        $this->addFlash('success', 'Votre compte a bien été supprimé');
    
        return $this->redirectToRoute('app_home');
    }


}

