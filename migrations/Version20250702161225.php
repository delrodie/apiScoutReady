<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250702161225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE complementaire (id INT AUTO_INCREMENT NOT NULL, scout_id INT DEFAULT NULL, branche_origine VARCHAR(255) DEFAULT NULL, base_niveau1 VARCHAR(255) DEFAULT NULL, annee_base_niveau1 INT DEFAULT NULL, base_niveau2 VARCHAR(255) DEFAULT NULL, annee_base_niveau2 INT DEFAULT NULL, avance_niveau1 VARCHAR(255) DEFAULT NULL, annee_avance_niveau1 INT DEFAULT NULL, avance_niveau2 VARCHAR(255) DEFAULT NULL, annee_avance_niveau2 INT DEFAULT NULL, avance_niveau3 VARCHAR(255) DEFAULT NULL, annee_avance_niveau3 INT DEFAULT NULL, avance_niveau4 VARCHAR(255) DEFAULT NULL, annee_avance_niveau4 INT DEFAULT NULL, UNIQUE INDEX UNIQ_D5F5D339486EE6BB (scout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complementaire ADD CONSTRAINT FK_D5F5D339486EE6BB FOREIGN KEY (scout_id) REFERENCES scout (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complementaire DROP FOREIGN KEY FK_D5F5D339486EE6BB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE complementaire
        SQL);
    }
}
