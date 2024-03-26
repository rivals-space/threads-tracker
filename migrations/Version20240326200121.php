<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240326200121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE watch (id UUID NOT NULL, watcher VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_500B4A26B28FE6AC ON watch (watcher)');
        $this->addSql('COMMENT ON COLUMN watch.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE watch_threads_user (watch_id UUID NOT NULL, threads_user_id UUID NOT NULL, PRIMARY KEY(watch_id, threads_user_id))');
        $this->addSql('CREATE INDEX IDX_A3FDD438C7C58135 ON watch_threads_user (watch_id)');
        $this->addSql('CREATE INDEX IDX_A3FDD438B8520083 ON watch_threads_user (threads_user_id)');
        $this->addSql('COMMENT ON COLUMN watch_threads_user.watch_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN watch_threads_user.threads_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE watch_threads_user ADD CONSTRAINT FK_A3FDD438C7C58135 FOREIGN KEY (watch_id) REFERENCES watch (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE watch_threads_user ADD CONSTRAINT FK_A3FDD438B8520083 FOREIGN KEY (threads_user_id) REFERENCES threads_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE watch_threads_user DROP CONSTRAINT FK_A3FDD438C7C58135');
        $this->addSql('ALTER TABLE watch_threads_user DROP CONSTRAINT FK_A3FDD438B8520083');
        $this->addSql('DROP TABLE watch');
        $this->addSql('DROP TABLE watch_threads_user');
    }
}
