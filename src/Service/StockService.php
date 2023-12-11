<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\StockStorageProductRepository;
use App\Repository\StorageRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StockService
{

    public function __construct(private StockStorageProductRepository $stockStorageProductRepository,
                                private StorageService             $storageService,
                                private ProductRepository             $productRepository,
    )
    {
    }

    public function updateStock(string $storageCode, string $productName, int $quantity): array
    {
        $storage = $this->storageService->getStorageByCode($storageCode);

        if (!$storage) {
            return [
                'data' => [
                    'message' => 'Hatalı Depo Kodu lütfen kontrol ediniz.',
                ],
                'status' => Response::HTTP_NOT_FOUND,
            ];
        }

        $product = $this->productRepository->getProduct($productName);

        $stockStorageProduct = $this->stockStorageProductRepository->add($storage, $product, $quantity);

        return [
            'data' => [
                'product_name' => $stockStorageProduct->getProduct()->getName(),
                'storage_name' => $stockStorageProduct->getStorage()->getName(),
                'quantity' => $stockStorageProduct->getQuantity(),
            ],
            'status' => Response::HTTP_CREATED,
        ];
    }
}
