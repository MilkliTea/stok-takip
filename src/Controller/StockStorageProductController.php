<?php

namespace App\Controller;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Entity\StockStorageProduct;
use App\Repository\StockStorageProductRepository;
use App\Service\StockService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StockStorageProductController extends AbstractController
{

    public function __construct(public StockService $stockService)
    {
    }

    #[Route('/stock-storage-product/update-stock/{storageCode}/{productName}/{quantity}', name: 'update-stock')]
    public function updateStock(string $storageCode, string $productName, int $quantity): JsonResponse
    {
        $result = $this->stockService->updateStock($storageCode, $productName, $quantity);

        return new JsonResponse(
            $result
        );
    }
}
