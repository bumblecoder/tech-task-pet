<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250808125737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pet (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', type BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', breed BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(55) NOT NULL, date_of_birth DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', approximate_age INT DEFAULT NULL, sex VARCHAR(10) NOT NULL, is_dangerous TINYINT(1) NOT NULL, INDEX IDX_E4529B858CDE5729 (type), INDEX IDX_E4529B85F8AF884F (breed), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pet_breed (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', type BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(55) NOT NULL, INDEX IDX_55D348EC8CDE5729 (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pet_type (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(55) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pet ADD CONSTRAINT FK_E4529B858CDE5729 FOREIGN KEY (type) REFERENCES pet_type (id)');
        $this->addSql('ALTER TABLE pet ADD CONSTRAINT FK_E4529B85F8AF884F FOREIGN KEY (breed) REFERENCES pet_breed (id)');
        $this->addSql('ALTER TABLE pet_breed ADD CONSTRAINT FK_55D348EC8CDE5729 FOREIGN KEY (type) REFERENCES pet_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pet DROP FOREIGN KEY FK_E4529B858CDE5729');
        $this->addSql('ALTER TABLE pet DROP FOREIGN KEY FK_E4529B85F8AF884F');
        $this->addSql('ALTER TABLE pet_breed DROP FOREIGN KEY FK_55D348EC8CDE5729');
        $this->addSql('DROP TABLE pet');
        $this->addSql('DROP TABLE pet_breed');
        $this->addSql('DROP TABLE pet_type');
    }
}
