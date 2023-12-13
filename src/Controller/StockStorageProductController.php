<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\StockService;
use App\Service\StorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StockStorageProductController extends AbstractController
{
    public function __construct(public StockService $stockService, public StorageService $storageService, public ProductRepository $productRepository)
    {
    }

    #[Route('/api/stock', name: 'add-stock', methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $storage = $this->storageService->getStorageByCode($data['storage']);

        if (!$storage) {
            return new JsonResponse([
                'message' => 'Hatalı Depo Kodu lütfen kontrol ediniz.',
            ], Response::HTTP_NOT_FOUND);
        }

        $result = $this->stockService->addStock(
            $storage,
            $data['product'],
            $data['quantity']
        );

        return new JsonResponse(
            $result,
            Response::HTTP_CREATED
        );
    }

    #[Route('/api/stock/{storageCode}/{productName}', name: 'check-stock', methods: 'GET')]
    public function show(Request $request): JsonResponse
    {
        $storageCode = $request->get('storageCode');
        $productName = $request->get('productName');

        $result = $this->stockService->getStockStorageProduct($storageCode, $productName);

        if (Response::HTTP_NOT_FOUND === $result['status']) {
            return new JsonResponse([
                'message' => $result['message'],
            ], Response::HTTP_NOT_FOUND);
        }

        $stockStorageProduct = $this->stockService->checkStock($result['data']);

        if (null === $stockStorageProduct) {
            return new JsonResponse([
                'message' => 'Depoda Ürün Bulunamadı.',
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            $stockStorageProduct,
            Response::HTTP_CREATED
        );
    }

    #[Route('/api/stock/edit/{storageCode}/{productName}', name: 'update-stock', methods: 'PATCH')]
    public function edit(string $storageCode, string $productName, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $result = $this->stockService->getStockStorageProduct($storageCode, $productName);

        if (Response::HTTP_NOT_FOUND === $result['status']) {
            return new JsonResponse([
                'message' => $result['message'],
            ], Response::HTTP_NOT_FOUND);
        }

        $stockStorageProduct = $this->stockService->updateStock($result['data'], $requestData['quantity']);

        return new JsonResponse(
            $stockStorageProduct,
            Response::HTTP_CREATED
        );
    }

    #[Route('/api/stock/{storageCode}/{productName}', name: 'delete-stock', methods: 'DELETE')]
    public function delete(string $storageCode, string $productName): JsonResponse
    {
        $result = $this->stockService->getStockStorageProduct($storageCode, $productName);

        if (Response::HTTP_NOT_FOUND === $result['status']) {
            return new JsonResponse([
                'message' => $result['message'],
            ], Response::HTTP_NOT_FOUND);
        }

        $this->stockService->deleteStock($result['data']);

        return new JsonResponse(
            'Ürün Depodan Başarıyla Silindi.',
            Response::HTTP_OK
        );
    }
}
