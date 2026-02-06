<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260130141414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mois (id INT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mois_conseil (mois_id INT NOT NULL, conseil_id INT NOT NULL, INDEX IDX_C48D2148FA0749B8 (mois_id), INDEX IDX_C48D2148668A3E03 (conseil_id), PRIMARY KEY (mois_id, conseil_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE mois_conseil ADD CONSTRAINT FK_C48D2148FA0749B8 FOREIGN KEY (mois_id) REFERENCES mois (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mois_conseil ADD CONSTRAINT FK_C48D2148668A3E03 FOREIGN KEY (conseil_id) REFERENCES conseil (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conseil DROP list_mois');
        $this->addSql('ALTER TABLE user DROP token');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mois_conseil DROP FOREIGN KEY FK_C48D2148FA0749B8');
        $this->addSql('ALTER TABLE mois_conseil DROP FOREIGN KEY FK_C48D2148668A3E03');
        $this->addSql('DROP TABLE mois');
        $this->addSql('DROP TABLE mois_conseil');
        $this->addSql('ALTER TABLE conseil ADD list_mois LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD token VARCHAR(255) DEFAULT NULL');
    }
}
