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
                'data' => [
                    'message' => 'Hatalı Depo Kodu lütfen kontrol ediniz.',
                ],
            ], Response::HTTP_NOT_FOUND);
        }

        $result = $this->stockService->addStock(
            $data['storage'],
            $data['product'],
            $data['quantity']
        );

        return new JsonResponse([
            'data' => $result,
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/stock/{storageCode}/{productName}', name: 'check-stock', methods: 'GET')]
    public function show(Request $request): JsonResponse
    {
        $storageCode = $request->get('storageCode');
        $productName = $request->get('productName');

        $storage = $this->storageService->getStorageByCode($storageCode);

        if (!$storage) {
            return new JsonResponse([
                'data' => [
                    'message' => 'Hatalı Depo Kodu lütfen kontrol ediniz.',
                ],
            ], Response::HTTP_NOT_FOUND);
        }

        $product = $this->productRepository->findOneBy([
            'name' => $productName
        ]);

        if (!$product) {
            return new JsonResponse([
                'data' => [
                    'message' => 'Ürün Bulunamadı.',
                ],
            ], Response::HTTP_NOT_FOUND);
        }


        $result = $this->stockService->checkStock(
            $storage,
            $product
        );

        if ($result === null) {
            return new JsonResponse([
                'data' => [
                    'message' => $storage->getName() . 'deposunda ' . $product->getName() . ' ürünü bulunamadı.'
                ]
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'data' => $result,
        ], Response::HTTP_CREATED);
    }


}
