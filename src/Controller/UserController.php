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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class UserController extends AbstractController
{

    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }
    
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
    public function deleteAccount(EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();

        // Vérifie si l'utilisateur existe
        if (!$user instanceof User) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('app_home');
        }

        // Invalide la session pour déconnecter l'utilisateur
        $request->getSession()->invalidate();

        // Supprime le token de sécurité pour assurer la déconnexion
        $this->container->get('security.token_storage')->setToken(null);

        try {
            $entityManager->remove($user);
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
            return $this->redirectToRoute('app_account', [
                'error' => 'Une erreur est survenue lors de la suppression de votre compte. Réessayez ultérieurement.'
            ]);
        }

        $this->addFlash('success', 'Votre compte a bien été supprimé');

        return $this->redirectToRoute('app_home');
    }

    #[Route('/account/api_access', name: 'api_access')]
    public function toggleApiAccess(): RedirectResponse
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        // Toggle API access
        $isApiAccessEnabled = !$user->isApiAccessEnabled();
        $user->setApiAccessEnabled($isApiAccessEnabled);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_account');
    }
    


}

