<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231111195751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Company and Branch tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE Branch (id INT AUTO_INCREMENT NOT NULL, street VARCHAR(250) NOT NULL, city VARCHAR(250) NOT NULL, postal_code INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Company (id INT AUTO_INCREMENT NOT NULL, branch_id INT NOT NULL, company_id INT NOT NULL, name VARCHAR(250) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_800230D3DCD6CC49 (branch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Company ADD CONSTRAINT FK_800230D3DCD6CC49 FOREIGN KEY (branch_id) REFERENCES Branch (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE Company DROP FOREIGN KEY FK_800230D3DCD6CC49');
        $this->addSql('DROP TABLE Branch');
        $this->addSql('DROP TABLE Company');
    }
}
