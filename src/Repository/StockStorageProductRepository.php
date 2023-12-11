<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\StockStorageProduct;
use App\Entity\Storage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockStorageProduct>
 *
 * @method StockStorageProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockStorageProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockStorageProduct[]    findAll()
 * @method StockStorageProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockStorageProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockStorageProduct::class);
    }

    //    /**
    //     * @return StockStorageProduct[] Returns an array of StockStorageProduct objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?StockStorageProduct
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function add(Storage $storage, Product $product, $quantity): StockStorageProduct
    {
        $stockStorageProduct = $this->findOneBy([
            'storage' => $storage,
            'product' => $product,
        ]);
        if (!$stockStorageProduct) {
            $stockStorageProduct = new StockStorageProduct();
            $stockStorageProduct->setStorage($storage);
            $stockStorageProduct->setProduct($product);
        }

        $stockStorageProduct->setQuantity($quantity);

        $this->getEntityManager()->persist($stockStorageProduct);
        $this->getEntityManager()->flush();

        return $stockStorageProduct;
    }
}
