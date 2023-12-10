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
                                private StorageRepository             $storageRepository,
                                private ProductRepository             $productRepository,
    )
    {
    }

    public function updateStock(string $storageCode, string $productName, int $quantity): array
    {
        $storage = $this->storageRepository->findOneBy([
            'code' => $storageCode
        ]);

        if (!$storage) {
            return [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Hatalı Depo Kodu lütfen kontrol ediniz.',
            ];
        }

        $product = $this->productRepository->getProduct($productName);

        $stockStorageProduct = $this->stockStorageProductRepository->add($storage, $product, $quantity);

        return [
            'status' => Response::HTTP_CREATED,
            'message' => 'Depodaki ürün adeti güncellendi',
            'product_name' => $stockStorageProduct->getProduct()->getName(),
            'storage_name' => $stockStorageProduct->getStorage()->getName(),
            'quantity' => $stockStorageProduct->getQuantity(),
        ];
    }
}
