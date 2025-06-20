<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250620055103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE groupe CHANGE paroisse paroisse VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_4B98C219068949C ON groupe (paroisse)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_1768816412B2DC9C ON scout
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE scout CHANGE matricule matricule VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_4B98C219068949C ON groupe
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE groupe CHANGE paroisse paroisse VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE scout CHANGE matricule matricule VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_1768816412B2DC9C ON scout (matricule)
        SQL);
    }
}
