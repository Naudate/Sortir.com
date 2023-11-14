<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231114133136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD is_change_password TINYINT(1) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_43C3D9C3CC94AC37 ON ville (code_postal)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_43C3D9C3CC94AC37 ON ville');
        $this->addSql('ALTER TABLE `user` DROP is_change_password');
    }
}
