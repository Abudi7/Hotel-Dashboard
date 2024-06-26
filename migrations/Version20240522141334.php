<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522141334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, startdate DATETIME NOT NULL, enddate DATETIME NOT NULL, customername VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rooms ADD booking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rooms ADD CONSTRAINT FK_7CA11A963301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('CREATE INDEX IDX_7CA11A963301C60 ON rooms (booking_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rooms DROP FOREIGN KEY FK_7CA11A963301C60');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP INDEX IDX_7CA11A963301C60 ON rooms');
        $this->addSql('ALTER TABLE rooms DROP booking_id');
    }
}
