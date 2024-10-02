<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\SerializerInterface;

class ApiProductController extends AbstractController
{

    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private Security $security)
    {
    }


    /**
     * Route pour récupérer tous les produits en base de données
     */
    #[OA\Tag(name: 'Products')]
    #[OA\Response(
        response: 200,
        description: 'Liste des produits récupérée avec succès'
    )]
    #[OA\Response(
        response: 400,
        description: 'Requête invalide'
    )]
    #[OA\Response(
        response: 403,
        description: 'Accès API non activé'
    )]
    #[Route('/api/products', name: 'app_api_product', methods: ['GET'])]
    public function getProductList(): JsonResponse
    {
        $user = $this->security->getUser();
        // Vérifier si l'utilisateur est authentifié et si l'accès API est activé
        if (!$user instanceof User || !$user->isApiAccessEnabled()) {
            return new JsonResponse(['message' => 'Accès API non activé'], Response::HTTP_FORBIDDEN);
        }

        $productList = $this->productRepository->findAll();
        if (empty($productList)) {
            return new JsonResponse([], Response::HTTP_OK); // Aucun produit à retourner
        }

        $jsonProductList = $this->serializer->serialize($productList, 'json', ['groups' => 'getProducts']);

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    
}
