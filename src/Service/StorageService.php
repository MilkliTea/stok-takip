<?php

namespace App\Service;

use App\Entity\Storage;
use App\Repository\StorageRepository;

class StorageService
{
    public function __construct(
        private StorageRepository $storageRepository,
    )
    {
    }

    public function getStorageByCode(string $code): ?Storage
    {
        $storage = $this->storageRepository->findOneBy([
            'code' => $code
        ]);

        if (!$storage) {
            return null;
        }

        return $storage;
    }
}