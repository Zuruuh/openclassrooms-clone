<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211126024053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE reset_user_password_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE reset_user_password_token (id INT NOT NULL, issuer_id INT NOT NULL, token VARCHAR(128) NOT NULL, issued_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FFC7B64DBB9D6FEE ON reset_user_password_token (issuer_id)');
        $this->addSql('COMMENT ON COLUMN reset_user_password_token.issued_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE reset_user_password_token ADD CONSTRAINT FK_FFC7B64DBB9D6FEE FOREIGN KEY (issuer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE reset_user_password_token_id_seq CASCADE');
        $this->addSql('DROP TABLE reset_user_password_token');
    }
}
