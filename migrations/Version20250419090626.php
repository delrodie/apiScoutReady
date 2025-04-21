<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250419090626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE asn (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, sigle VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE district (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, INDEX IDX_31C1548798260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE groupe (id INT AUTO_INCREMENT NOT NULL, district_id INT DEFAULT NULL, paroisse VARCHAR(255) DEFAULT NULL, INDEX IDX_4B98C21B08FA272 (district_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, asn_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, symbolique VARCHAR(255) DEFAULT NULL, INDEX IDX_F62F1768EE17B51 (asn_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE scout (id INT AUTO_INCREMENT NOT NULL, groupe_id INT DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, matricule VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, sexe VARCHAR(255) DEFAULT NULL, date_naissance DATE DEFAULT NULL, lieu_naissance VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, telephone_parent TINYINT(1) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, fonction VARCHAR(255) DEFAULT NULL, branche VARCHAR(255) DEFAULT NULL, statut VARCHAR(255) DEFAULT NULL, qr_code VARCHAR(255) DEFAULT NULL, INDEX IDX_176881647A45358C (groupe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE district ADD CONSTRAINT FK_31C1548798260155 FOREIGN KEY (region_id) REFERENCES region (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21B08FA272 FOREIGN KEY (district_id) REFERENCES district (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE region ADD CONSTRAINT FK_F62F1768EE17B51 FOREIGN KEY (asn_id) REFERENCES asn (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE scout ADD CONSTRAINT FK_176881647A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE district DROP FOREIGN KEY FK_31C1548798260155
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C21B08FA272
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE region DROP FOREIGN KEY FK_F62F1768EE17B51
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE scout DROP FOREIGN KEY FK_176881647A45358C
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE asn
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE district
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE groupe
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE region
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE scout
        SQL);
    }
}
