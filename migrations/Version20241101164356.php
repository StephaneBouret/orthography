<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241101164356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sections (id INT AUTO_INCREMENT NOT NULL, program_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, short_description LONGTEXT DEFAULT NULL, INDEX IDX_2B9643983EB8070A (program_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sections ADD CONSTRAINT FK_2B9643983EB8070A FOREIGN KEY (program_id) REFERENCES program (id)');
        $this->addSql('ALTER TABLE courses ADD section_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4CD823E37A FOREIGN KEY (section_id) REFERENCES sections (id)');
        $this->addSql('CREATE INDEX IDX_A9A55A4CD823E37A ON courses (section_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE courses DROP FOREIGN KEY FK_A9A55A4CD823E37A');
        $this->addSql('ALTER TABLE sections DROP FOREIGN KEY FK_2B9643983EB8070A');
        $this->addSql('DROP TABLE sections');
        $this->addSql('DROP INDEX IDX_A9A55A4CD823E37A ON courses');
        $this->addSql('ALTER TABLE courses DROP section_id');
    }
}
