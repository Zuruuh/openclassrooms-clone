<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211126052504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE profile (id INT NOT NULL, owner_id INT NOT NULL, name VARCHAR(32) DEFAULT NULL, biography VARCHAR(255) DEFAULT NULL, birthday VARCHAR(10) DEFAULT NULL, website VARCHAR(64) DEFAULT NULL, linked_in VARCHAR(255) DEFAULT NULL, github VARCHAR(64) DEFAULT NULL, discord VARCHAR(32) DEFAULT NULL, country VARCHAR(64) DEFAULT NULL, profile_picture VARCHAR(128) NOT NULL, last_update TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8157AA0F7E3C61F9 ON profile (owner_id)');
        $this->addSql('COMMENT ON COLUMN profile.last_update IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD registered_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN "user".registered_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE profile_id_seq CASCADE');
        $this->addSql('DROP TABLE profile');
        $this->addSql('ALTER TABLE "user" DROP registered_at');
    }
}
