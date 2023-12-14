<?php

namespace App\Service;

use App\Entity\Product;
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

    public function addStock(Storage $storage, Product $product, int $quantity): array
    {
        $stockStorageProduct = $this->stockStorageProductRepository->add($storage, $product, $quantity);

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

    public function getStockStorageProduct(int $storageId, int $productId): array
    {
        $storage = $this->storageService->getStorageById($storageId);

        if (!$storage) {
            return [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Depo Bulunamadı.',
                'data' => null,
            ];
        }

        $product = $this->productRepository->find($productId);

        if (!$product) {
            return [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Ürün Bulunamadı.',
                'data' => null
            ];
        }

        $stockStorageProduct = $this->stockStorageProductRepository->findOneBy([
            'storage' => $storage,
            'product' => $product,
        ]);

        if (null === $stockStorageProduct) {
            return [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Ürün Depoda Mevcut Değil',
                'data' => null
            ];
        }

        return [
            'status' => Response::HTTP_FOUND,
            'message' => 'Ürün Depoda Mevcut',
            'data' => $stockStorageProduct,
        ];
    }
}
