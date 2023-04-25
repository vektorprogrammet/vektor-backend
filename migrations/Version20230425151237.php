<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425151237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE linjeforening (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, field_of_study_id INTEGER NOT NULL, name VARCHAR(50) NOT NULL, contact_person VARCHAR(70) DEFAULT NULL, CONSTRAINT FK_34409D589E9C46D5 FOREIGN KEY (field_of_study_id) REFERENCES field_of_study (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34409D589E9C46D5 ON linjeforening (field_of_study_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE linjeforening');
    }
}
