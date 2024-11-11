<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241107100054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_result DROP FOREIGN KEY FK_FE2E314A1E27F6BF');
        $this->addSql('ALTER TABLE quiz_result DROP FOREIGN KEY FK_FE2E314AF24C5BEC');
        $this->addSql('DROP INDEX IDX_FE2E314A1E27F6BF ON quiz_result');
        $this->addSql('DROP INDEX IDX_FE2E314AF24C5BEC ON quiz_result');
        $this->addSql('ALTER TABLE quiz_result ADD section_id INT DEFAULT NULL, DROP question_id, DROP selected_answer_id');
        $this->addSql('ALTER TABLE quiz_result ADD CONSTRAINT FK_FE2E314AD823E37A FOREIGN KEY (section_id) REFERENCES sections (id)');
        $this->addSql('CREATE INDEX IDX_FE2E314AD823E37A ON quiz_result (section_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_result DROP FOREIGN KEY FK_FE2E314AD823E37A');
        $this->addSql('DROP INDEX IDX_FE2E314AD823E37A ON quiz_result');
        $this->addSql('ALTER TABLE quiz_result ADD selected_answer_id INT DEFAULT NULL, CHANGE section_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz_result ADD CONSTRAINT FK_FE2E314A1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE quiz_result ADD CONSTRAINT FK_FE2E314AF24C5BEC FOREIGN KEY (selected_answer_id) REFERENCES answer (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_FE2E314A1E27F6BF ON quiz_result (question_id)');
        $this->addSql('CREATE INDEX IDX_FE2E314AF24C5BEC ON quiz_result (selected_answer_id)');
    }
}
