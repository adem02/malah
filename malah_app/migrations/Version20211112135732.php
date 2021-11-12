<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112135732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tags_product_product DROP FOREIGN KEY FK_90ACD5EF273D7ABB');
        $this->addSql('DROP TABLE tags_product');
        $this->addSql('DROP TABLE tags_product_product');
        $this->addSql('ALTER TABLE product ADD quantity INT NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD tags LONGTEXT DEFAULT NULL, ADD slug VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tags_product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tags_product_product (tags_product_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_90ACD5EF273D7ABB (tags_product_id), INDEX IDX_90ACD5EF4584665A (product_id), PRIMARY KEY(tags_product_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tags_product_product ADD CONSTRAINT FK_90ACD5EF273D7ABB FOREIGN KEY (tags_product_id) REFERENCES tags_product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tags_product_product ADD CONSTRAINT FK_90ACD5EF4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product DROP quantity, DROP created_at, DROP tags, DROP slug');
    }
}
