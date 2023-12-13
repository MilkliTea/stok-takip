<?php

namespace App\Service;

use App\Entity\StockStorageProduct;
use App\Entity\Storage;
use App\Repository\ProductRepository;
use App\Repository\StockStorageProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class StockService
{
    public function __construct(
        private StockStorageProductRepository $stockStorageProductRepository,
        private ProductRepository $productRepository,
        private StorageService $storageService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function addStock(Storage $storage, string $productName, int $quantity): array
    {
        $product = $this->productRepository->getProduct($productName);

        $stockStorageProduct = $this->stockStorageProductRepository->add($storage, $product, $quantity);

        return $this->prepareData($stockStorageProduct);
    }

    public function checkStock(?StockStorageProduct $stockStorageProduct): ?array
    {
        if (!$stockStorageProduct) {
            return null;
        }

        return $this->prepareData($stockStorageProduct);
    }

    public function updateStock(StockStorageProduct $stockStorageProduct, int $quantity): array
    {
        $stockStorageProduct->setQuantity($quantity);

        $this->entityManager->persist($stockStorageProduct);
        $this->entityManager->flush();

        return $this->prepareData($stockStorageProduct);
    }

    public function deleteStock(StockStorageProduct $stockStorageProduct): void
    {
        $this->entityManager->remove($stockStorageProduct);
        $this->entityManager->flush();

        return;
    }

    public function getStockStorageProduct(string $storageCode, string $productName): array
    {
        $storage = $this->storageService->getStorageByCode($storageCode);

        if (!$storage) {
            return [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Hatalı Depo Kodu lütfen kontrol ediniz.',
                'data' => null,
            ];
        }

        $product = $this->productRepository->findOneBy([
            'name' => $productName,
        ]);

        if (!$product) {
            return [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Ürün Bulunamadı.',
                'data' => null,
            ];
        }

        $stockStorageProduct = $this->stockStorageProductRepository->findOneBy([
            'storage' => $storage,
            'product' => $product,
        ]);

        return [
            'status' => Response::HTTP_OK,
            'message' => 'Depoda Ürün Mevcut',
            'data' => $this->prepareData($stockStorageProduct),
        ];
    }

    public function prepareData(StockStorageProduct $stockStorageProduct): array
    {
        return [
            'storage' => [
                'id' => $stockStorageProduct->getStorage()->getId(),
                'name' => $stockStorageProduct->getStorage()->getName(),
                'code' => $stockStorageProduct->getStorage()->getCode(),
            ],
            'product' => [
                'id' => $stockStorageProduct->getProduct()->getId(),
                'name' => $stockStorageProduct->getProduct()->getName(),
                'description' => $stockStorageProduct->getProduct()->getDescription(),
            ],
            'quantity' => $stockStorageProduct->getQuantity(),
        ];
    }
}
