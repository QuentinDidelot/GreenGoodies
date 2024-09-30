<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\SerializerInterface;

class ApiProductController extends AbstractController
{

    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer)
    {
    }


    /**
     * Route pour récupérer tous les produits en base de données
     */
    // #[OA\Tag(name:'Products')]
    #[Route('/api/products', name: 'app_api_product', methods: ['GET'])]
    public function getProductList () : JsonResponse {
        
        $productList = $this->productRepository->findAll();
        $jsonProductList = $this->serializer->serialize($productList, 'json', ['groups' => 'getProducts']);

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }
    
}
