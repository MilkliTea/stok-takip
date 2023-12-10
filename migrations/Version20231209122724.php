<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231209122724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stock_storage_product (product_id INT NOT NULL, storage_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_45DF4C6E4584665A (product_id), INDEX IDX_45DF4C6E5CC5DB90 (storage_id), PRIMARY KEY(product_id, storage_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stock_storage_product ADD CONSTRAINT FK_45DF4C6E4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE stock_storage_product ADD CONSTRAINT FK_45DF4C6E5CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_storage_product DROP FOREIGN KEY FK_45DF4C6E4584665A');
        $this->addSql('ALTER TABLE stock_storage_product DROP FOREIGN KEY FK_45DF4C6E5CC5DB90');
        $this->addSql('DROP TABLE stock_storage_product');
    }
}
